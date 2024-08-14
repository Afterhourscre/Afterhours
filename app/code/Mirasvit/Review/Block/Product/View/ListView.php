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


namespace Mirasvit\Review\Block\Product\View;


use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\ProductTypes\ConfigInterface as ProductTypeConfig;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Json\EncoderInterface as JsonEncoder;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Url\EncoderInterface as UrlEncoder;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as MagentoReviewCollectionFactory;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Model\Config\Source\SortingSource;
use Mirasvit\Review\Model\ConfigProvider;
use Mirasvit\Review\Model\ResourceModel\ReviewSummary\CollectionFactory as ReviewSummaryCollection;
use Mirasvit\Review\Model\ReviewSummary;
use Mirasvit\Review\Repository\ReviewRepository;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ListView extends \Magento\Review\Block\Product\View\ListView
{
    public    $clearCollection;

    protected $reviewSummaryFactory;

    protected $sortingSource;

    protected $productRepository;

    private   $reviewRepository;

    private   $reviewCollection;

    private   $configProvider;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ReviewSummaryCollection        $reviewSummaryFactory,
        SortingSource                  $sortingSource,
        ConfigProvider                 $configProvider,
        ReviewRepository               $reviewRepository,
        Context                        $context,
        UrlEncoder                     $urlEncoder,
        JsonEncoder                    $jsonEncoder,
        StringUtils                    $string,
        ProductHelper                  $productHelper,
        ProductTypeConfig              $productTypeConfig,
        LocaleFormat                   $localeFormat,
        CustomerSession                $customerSession,
        ProductRepositoryInterface     $productRepository,
        PriceCurrencyInterface         $priceCurrency,
        MagentoReviewCollectionFactory $collectionFactory,
        array                          $data = []
    ) {
        $this->reviewSummaryFactory = $reviewSummaryFactory;
        $this->sortingSource        = $sortingSource;
        $this->configProvider       = $configProvider;
        $this->reviewRepository     = $reviewRepository;
        $this->productRepository    = $productRepository;

        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $collectionFactory,
            $data
        );
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getReviewsCollection()
    {
        if (!$this->reviewCollection) {
            $this->reviewCollection = $this->reviewRepository->getCollection()->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                ReviewInterface::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $this->getProduct()->getId()
            );

            $this->reviewCollection->getSelect()
                ->joinLeft(['rating' => $this->reviewCollection->getResource()->getTable('rating_option_vote')],
                    'main_table.review_id = rating.review_id ',
                    ['total_rating' => new \Zend_Db_Expr('ROUND(SUM(rating.percent)/COUNT(*)/20)')])
                ->group('rating.review_id');

            if ($params = $this->getRequest()->getParams()) {
                foreach ($params as $key => $value) {
                    switch ($key) {
                        case ReviewInterface::IS_VERIFIED_BUYER :
                            $this->reviewCollection->addFieldToFilter($key, ['eq' => true]);

                            break;
                        case 'rating':
                            $value = implode(',', $value);
                            $this->reviewCollection->getSelect()->having("total_rating in ($value)");

                            break;
                        case 'sorting':
                            $sorting = explode('-', $value);
                            $this->reviewCollection->setOrder($sorting[0], $sorting[1]);
                            if ($sorting[0] != 'created_at') {
                                $this->reviewCollection->addOrder('created_at', 'desc');
                            }

                            break;
                    }
                }
            }
        }

        if (!$this->getRequest()->getParam('sorting')) {
            $sorting = explode('-', $this->configProvider->getDefaultSorting());
            $this->reviewCollection->setOrder($sorting[0], $sorting[1]);
            if ($sorting[0] != 'created_at') {
                $this->reviewCollection->addOrder('created_at', 'desc');
            }
        }

        return $this->reviewCollection; // TODO: Change the autogenerated stub
    }

    public function getProductReviewUrl()
    {
        return $this->getUrl(
            'review/product/listAjax',
            [
                '_secure' => $this->getRequest()->isSecure(),
                'id'      => $this->getProductId(),
            ]
        );
    }

    public function getSortingOptions(): array
    {
        $sortOptions = [];

        $sorting = $this->getRequest()->getParam('sorting');

        foreach ($this->sortingSource->toOptionArray() as $sortOption) {
            if ($sorting && $sorting == $sortOption['value']) {
                $sortOption['selected'] = true;
            } else {
                if (!$sorting && $sortOption['value'] == $this->configProvider->getDefaultSorting()) {
                    $sortOption['selected'] = true;
                } else {
                    $sortOption['selected'] = false;
                }
            }
            $sortOptions[] = $sortOption;
        }

        return $sortOptions;
    }

    public function getReviewsCount(int $rating = null): int
    {
        $reviewCount = 0;

        $collection = clone $this->reviewRepository->getCollection()->addStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addStatusFilter(
            ReviewInterface::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $this->getProduct()->getId()
        );

        $collection->getSelect()
            ->joinLeft(['rating' => $collection->getResource()->getTable('rating_option_vote')],
                'main_table.review_id = rating.review_id ',
                ['total_rating' => new \Zend_Db_Expr('ROUND(SUM(rating.percent)/COUNT(*)/20)')])
            ->group('rating.review_id');

        if ($params = $this->getRequest()->getParams()) {
            foreach ($params as $key => $value) {
                switch ($key) {

                    case ReviewInterface::IS_VERIFIED_BUYER:
                        $collection->addFieldToFilter($key, ['eq' => true]);
                        break;

                }
            }
        }

        if (!is_null($rating)) {
            $collection->getSelect()->having("total_rating = $rating");
        }

        return $collection->getSize();

    }

    public function getFilterOptions(): array
    {
        $rating = $this->getRequest()->getParam('rating') ?? [];

        return [
            [
                'label'   => '5 Star',
                'value'   => 5,
                'checked' => in_array(5, $rating),
            ],
            [
                'label'   => '4 Star or more',
                'value'   => 4,
                'checked' => in_array(4, $rating),
            ],
            [
                'label'   => '3 Star or more',
                'value'   => 3,
                'checked' => in_array(3, $rating),
            ],
            [
                'label'   => '2 Star or more',
                'value'   => 2,
                'checked' => in_array(2, $rating),
            ],
            [
                'label'   => '1 Star or more',
                'value'   => 1,
                'checked' => in_array(1, $rating),
            ],
        ];
    }

    public function getReviewSummary(): ?ReviewSummary
    {
        $collection = $this->reviewSummaryFactory->create();
        $collection->addFieldToFilter('product_id', ['eq' => $this->getProductId()])
            ->setOrder('generated_at', 'desc');

        $reviewSummary = $collection->getFirstItem();

        if (!$reviewSummary || !$reviewSummary->getId()) {
            return null;
        }

        if ($ratingSummary = $this->getRatingSummary()) {
            $reviewSummary->addData($ratingSummary);
        }

        return $reviewSummary;
    }

    public function getRatingSummary(): ?array
    {
        $productId = $this->getRequest()->getParam('id');

        if (!$productId) {
            return null;
        }

        $product = $this->productRepository->getById($productId);

        if (!$product || !$product->getId()) {
            return null;
        }

        $this->reviewRepository->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());

        return [
            'rating_summary' => $product->getRatingSummary()->getRatingSummary(),
            'reviews_count'  => $product->getRatingSummary()->getReviewsCount(),
        ];
    }

    public function isDisplayFilters(): bool
    {
        return !$this->getData('is_product_fullwidth')
            && (
                $this->getReviewsCount() >= 2
                || $this->getReviewsCount() < 2 && count($this->getRequest()->getParams()) > 1
            );
    }

    public function getVerifiedReviewsCount(): int
    {
        return (int)$this->reviewRepository->getCollection()->addStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addStatusFilter(
            ReviewInterface::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $this->getProduct()->getId()
        )->addFieldToFilter(
            ReviewInterface::IS_VERIFIED_BUYER, ['eq' => true]
        )->count();
    }

    public function isShowGallery(): bool
    {
        $images = $this->getChildBlock('summary.gallery')->setProductId((int)$this->getProductId())->getImages();

        return $this->configProvider->displayReviewPhotoGallery() && count($images);
    }
}
