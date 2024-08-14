<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Review\Service;

use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Repository\ReviewRepository;
use Mirasvit\Review\Repository\ReviewSummaryRepository;
use Mirasvit\Review\Service\CompletionService;
use Mirasvit\Review\Api\Data\ReviewSummaryInterfaceFactory;
use  Mirasvit\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Mirasvit\Review\Model\ResourceModel\ReviewSummary as ResourceModel;
use Mirasvit\Review\Api\Data\ReviewSummaryInterface;
use Mirasvit\Review\Model\ResourceModel\ReviewSummary\CollectionFactory as SummaryCollectionFactory;
use Mirasvit\Review\Model\ConfigProvider;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReviewAggregatorService
{
    protected $reviewCollection;

    protected $reviewRepository;

    protected $completionService;

    protected $reviewSummaryFactory;

    protected $configProvider;

    protected $_logger;

    protected $summaryCollectionFactory;

    protected $resourceModel;

    protected $storeManager;

    protected $reviewSummaryRepository;

    protected $errorProducts = [];

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct
    (
        ReviewCollection              $reviewCollection,
        ResourceModel                 $resourceModel,
        SummaryCollectionFactory      $summaryCollectionFactory,
        LoggerInterface               $logger,
        ConfigProvider                $configProvider,
        ReviewSummaryInterfaceFactory $reviewSummaryFactory,
        ReviewRepository              $reviewRepository,
        CompletionService             $completionService,
        StoreManagerInterface         $storeManager,
        ReviewSummaryRepository       $reviewSummaryRepository
    ) {
        $this->reviewCollection         = $reviewCollection;
        $this->resourceModel            = $resourceModel;
        $this->summaryCollectionFactory = $summaryCollectionFactory;
        $this->_logger                  = $logger;
        $this->configProvider           = $configProvider;
        $this->reviewSummaryFactory     = $reviewSummaryFactory;
        $this->completionService        = $completionService;
        $this->reviewRepository         = $reviewRepository;
        $this->storeManager             = $storeManager;
        $this->reviewSummaryRepository  = $reviewSummaryRepository;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute(?int $productId = null)
    {
        if (!$this->configProvider->isAggregationEnabled()) {
            return false;
        }

        foreach ($this->storeManager->getStores() as $store) {
            $storeId = $store->getId();

            $reviewsCollection = $this->reviewRepository->getCollection()
                ->addStoreFilter($storeId)
                ->setOrder(ReviewInterface::PRODUCT_ID, 'asc')
                ->setOrder(ReviewInterface::CREATED_AT, 'desc');

            if ($productId) {
                $reviewsCollection->addFieldToFilter(ReviewInterface::PRODUCT_ID, ['eq' => $productId]);
            }

            $reviewsPerProduct = [];

            foreach ($reviewsCollection as $item) {
                $reviewsPerProduct[$item->getData(ReviewInterface::PRODUCT_ID)][] = [
                    ReviewInterface::DETAIL => $item->getDetail(),
                    ReviewInterface::PROS   => $item->getPros(),
                    ReviewInterface::CONS   => $item->getCons(),
                ];
            }

            foreach ($reviewsPerProduct as $productId => $reviews) {
                if (count($reviews) < $this->configProvider->getCountReviewLimit()) {
                    continue;
                }

                $lastReviewId = max(array_keys($reviews));

                $reviewSummary = $this->reviewSummaryRepository->getByProductAndStore((int)$productId, (int)$storeId);

                if (!$reviewSummary) {
                    $reviewSummary = $this->reviewSummaryRepository->create();
                }

                if ($reviewSummary->getId() && !$this->isNewReviewsExist((int)$reviewSummary->getLastReviewId(), $reviews)) {
                    continue;
                }

                $request = $this->aggregateReviewRequest($reviews);

                $countReview = count($reviews);
                $response    = $this->completionService->answer($request);
                $result      = $this->processResponse($response);

                if (empty($result)) {
                    $message               = "Review data for product $productId on $storeId not received, review not generated";
                    $this->errorProducts[] = $productId;
                    $this->_logger->error($message);
                    continue;
                }

                $aggregatedReview = [
                    ReviewSummaryInterface::STORE_ID          => $storeId,
                    ReviewSummaryInterface::DETAIL            => $result['detail'],
                    ReviewSummaryInterface::PROS              => implode(PHP_EOL, $result['pros']),
                    ReviewSummaryInterface::CONS              => implode(PHP_EOL, $result['cons']),
                    ReviewSummaryInterface::LAST_REVIEW_ID    => $lastReviewId,
                    ReviewSummaryInterface::PROCESSED_REVIEWS => $countReview,
                    ReviewSummaryInterface::GENERATED_AT      => date_create()->format('Y-m-d H:i:s'),
                    ReviewSummaryInterface::PRODUCT_ID        => $productId,
                ];

                if ($reviewSummary->getSummaryId()) {
                    $aggregatedReview[ReviewSummaryInterface::ID] = $reviewSummary->getSummaryId();
                }

                $reviewSummary->setData($aggregatedReview);
                $this->reviewSummaryRepository->save($reviewSummary);
            }
        }

        if (!empty($this->errorProducts)) {
            return $this->errorProducts;
        }

        return true;
    }


    protected function validateLenght(string $request, string $requestReview): bool
    {
        $limit = $this->configProvider->getLenghtLimit();

        return strlen($request . $requestReview) < $limit;
    }

    protected function processResponse(string $response): array
    {
        $result = SerializeService::decode($response);

        if (!is_array($result)) {
            return [];
        }

        $result['pros'] = str_replace('N/A', '', $result['pros']);
        $result['cons'] = str_replace('N/A', '', $result['cons']);

        return $result;
    }

    protected function aggregateReviewRequest($reviews): string
    {
        $prompt = "Write a Summarized Review for product based on following reviews:" . PHP_EOL;

        $counter = 1;

        foreach ($reviews as $review) {

            if ($counter >= $this->configProvider->getMaxReviewCountToAggregate()) {
                break;
            }

            $formattedReview = "$counter." . PHP_EOL;
            $formattedReview .= $this->reviewToSting($review);

            if (!$this->validateLenght($prompt . $this->getPromptAdditionalInstructions(), $formattedReview)) {
                break;
            }

            $prompt .= $formattedReview;
            $counter++;
        }

        return $prompt . $this->getPromptAdditionalInstructions();
    }

    protected function isNewReviewsExist(int $lastReviewId, array $reviews): bool
    {
        $newReviews = [];
        foreach ($reviews as $reviewId => $review) {
            if ($reviewId > $lastReviewId) {
                $newReviews[] = $review;
            }
        }

        return !empty($newReviews);
    }

    private function getPromptAdditionalInstructions(): string
    {
        $additional = <<<text
        Return a JSON with the following fields:
        'detail' - review details
        'pros' - bullet list of positive points about the product
        'cons' - bullet list of negative points about the product
        Write in the same language as the original text.

        Summarized Review:

        text;

        return $additional;
    }

    private function reviewToSting(array $review): string
    {
        $reviewString = '';

        foreach ($review as $key => $data) {
            $reviewString .= $this->formatReviewDataByKey($review, $key);
        }

        return $reviewString;
    }

    private function formatReviewDataByKey(array $review, string $key): string
    {
        $formated = '';

        if (!isset($review[$key])) {
            return $formated;
        }

        $formated = ucfirst($key) . ':' . PHP_EOL;
        $data     = $review[$key];

        if (!trim((string)$data)) {
            return ucfirst($key) . ': N/A' . PHP_EOL;
        }

        switch ($key) {
            case 'pros':
            case 'cons':
                foreach (explode(PHP_EOL, $review[$key]) as $row) {
                    $row = trim($row);

                    if (!$row) {
                        continue;
                    }

                    $formated .= '- ' . $row . PHP_EOL;
                }

                break;
            case 'detail':
                $formated .= $data . PHP_EOL;
                break;
            default:
                break;
        }

        return $formated;
    }

}
