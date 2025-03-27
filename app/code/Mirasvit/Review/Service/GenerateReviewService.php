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



namespace Mirasvit\Review\Service;

use Magento\Review\Model\RatingFactory;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Service\AutowriteService;
use Magento\Catalog\Model\ProductRepository;
use Magento\Review\Model\Rating\Option\Vote;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Mirasvit\Review\Repository\ReviewRepository;
use Magento\Review\Model\ResourceModel\Rating\Collection as RatingCollection;
use Magento\Review\Model\ResourceModel\Rating\Option\CollectionFactory as VoteOptionCollectionFactory;


class GenerateReviewService
{
    private $autowriteService;

    private $productCollectionFactory;

    private $reviewRepository;

    private $ratingFactory;

    private $ratingCollection;

    private $voteOptionCollectionFactory;

    private $objectManager;

    private $errorReviews = [];

    private $iteration    = 0;

    public function __construct(
        Vote                        $vote,
        RatingCollection            $ratingCollection,
        VoteOptionCollectionFactory $voteOptionCollectionFactory,
        RatingFactory               $ratingFactory,
        ReviewRepository            $reviewRepository,
        ProductCollection           $productCollectionFactory,
        AutowriteService            $autowriteService
    ) {
        $this->objectManager               = \Magento\Framework\App\ObjectManager::getInstance();
        $this->ratingCollection            = $ratingCollection;
        $this->voteOptionCollectionFactory = $voteOptionCollectionFactory;
        $this->ratingFactory               = $ratingFactory;
        $this->reviewRepository            = $reviewRepository;
        $this->productCollectionFactory    = $productCollectionFactory;
        $this->autowriteService            = $autowriteService;

    }

    public function execute(
        string  $nickname,
        int     $rating,
        ?int    $productId = null,
        ?int    $store = null,
        ?string $additional = null,
        int     $qty = 1,
        bool    $autoapprove = false
    ) {
        $productIds = [];

        if (!$productId) {
            $productIds = $this->productCollectionFactory->create()->getAllIds();
        }

        for ($i = 0; $i < $qty; $i++) {

            if ($productId) {
                $reviewData = $this->generateReview((int)$productId, (string)$additional, $store);

                if (!is_array($reviewData)) {
                    $this->errorReviews[$productId] = "Too many tries to generate review for productId = $productId";
                    continue;
                }

                $reviewData[ReviewInterface::NICKNAME] = $nickname;
                $this->createReview($reviewData, (int)$productId, $rating, $autoapprove, $store);
            } else {
                foreach ($productIds as $productId) {
                    $reviewData = $this->generateReview((int)$productId, (string)$additional, $store);

                    if (!is_array($reviewData)) {
                        $this->errorReviews[$productId] = "Too many tries to generate review for productId = $productId";
                        continue;
                    }

                    $reviewData[ReviewInterface::NICKNAME] = $nickname;
                    $this->createReview($reviewData, (int)$productId, $rating, $autoapprove, $store);
                }
            }

        }

        if (!empty($this->errorReviews)) {
            return $this->errorReviews;
        }

        return true;
    }

    public function createReview(array $reviewData, int $productId, int $rating, bool $autoapprove, ?int $storeId = null): ?ReviewInterface
    {
        $newReview = $this->reviewRepository->create();
        $stores    = [];

        try {
            $newReview->setData($reviewData);

            if (isset($reviewData[ReviewInterface::PROS])) {
                $newReview->setPros(implode(PHP_EOL, $reviewData[ReviewInterface::PROS]));
            }
            if (isset($reviewData[ReviewInterface::CONS])) {
                $newReview->setCons(implode(PHP_EOL, $reviewData[ReviewInterface::CONS]));
            }

            $newReview->setProductId($productId)
                ->setEntityId($newReview->getEntityIdByCode('product'));

            if ($storeId) {
                $newReview->setStoreId($storeId);
                $stores[] = $storeId;
                $stores[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            } else {
                $newReview->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
                $stores[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }
            $newReview->setStores(array_unique($stores));


            if ($autoapprove) {
                $newReview->setStatusId(ReviewInterface::STATUS_APPROVED);
            } else {
                $newReview->setStatusId(ReviewInterface::STATUS_PENDING);
            }

            $newReview->getResource()->save($newReview);

            $this->addReviewRating($rating, $newReview);

        } catch (\Exception $e) {
            $this->errorReviews[$productId] = $e->getMessage();
        }

        return $newReview;

    }

    public function addReviewRating(int $rating, ReviewInterface $review): bool
    {
        if (!$review->getId()) {
            return false;
        }

        $arrRatingId = [];
        $ratingIds   = $this->ratingCollection->getAllIds();

        try {
            foreach ($ratingIds as $ratingId) {
                $option = $this->voteOptionCollectionFactory->create()->addFieldToFilter('value', $rating)
                    ->addRatingFilter($ratingId)
                    ->getFirstItem();

                $arrRatingId[$ratingId] = $option->getId();
            }

            foreach ($arrRatingId as $ratingId => $optionId) {
                $this->ratingFactory->create()
                    ->setRatingId($ratingId)
                    ->setReviewId($review->getId())
                    ->addOptionVote($optionId, $review->getProductId());
            }

            $review->aggregate();

        } catch (\Exception $e) {
            $this->errorReviews[$review->getProductId()] = $e->getMessage();
        }

        return true;

    }

    public function generateReview(int $productId, string $additional, ?int $store = null): ?array
    {
        if ($this->iteration == 3) {
            $this->iteration = 0;
            return null;
        }

        $generatedReview = $this->autowriteService->generateReview($productId, (string)$additional, $store);

        if ((!isset($generatedReview['review'][ReviewInterface::DETAIL])
                || !isset($generatedReview['review'][ReviewInterface::TITLE]))) {
            $this->iteration++;
            $reviewData = $this->generateReview($productId, $additional, $store);
        } else {
            $this->iteration = 0;
            $reviewData      = $generatedReview['review'];
        }

        return $reviewData;
    }
}
