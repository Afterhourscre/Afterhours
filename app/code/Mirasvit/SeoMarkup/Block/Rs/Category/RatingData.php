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
 * @package   mirasvit/module-seo
 * @version   2.0.154
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoMarkup\Block\Rs\Category;

use Magento\Review\Model\Rating;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Review\Model\Review;
use Mirasvit\SeoMarkup\Model\Config\ProductConfig;

class RatingData
{
    private $productConfig;

    private $reviewCollectionFactory;

    private $rating;

    public function __construct(
        ProductConfig $productConfig,
        ReviewCollectionFactory $reviewCollectionFactory,
        Rating $rating
    ) {
        $this->productConfig           = $productConfig;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->rating                  = $rating;
    }

    /**
     * @param array $productIds
     * @param object $store
     * @return array|false
     */
    public function getData($category)
    {
        $collection = $this->reviewCollectionFactory->create()
            ->addStatusFilter(Review::STATUS_APPROVED)
            ->setDateOrder();

        $collection->getSelect()->join(
            ['category_product' => $collection->getResource()->getTable('catalog_category_product')],
            'category_product.product_id = main_table.entity_pk_value', []);

        $collection->getSelect()->where('category_product.category_id = (?)', $category->getId());

        $data = false;

        if (count($collection)) {
            $ratingValue = 0;
            $ratingCount = 0;

            /** @var Review $review */
            foreach ($collection as $review) {
                $summary = $this->rating->getReviewSummary($review->getId());

                if ($summary->getSum() && $summary->getCount()) {
                    $ratingValue += $summary->getSum() / $summary->getCount() / 20;
                    $ratingCount += 1;
                }
            }

            if ($ratingCount && $ratingValue) {
                $data = [
                    "@type"       => "AggregateRating",
                    "ratingValue" => number_format($ratingValue / $ratingCount, 2),
                    "ratingCount" => $ratingCount,
                    "bestRating"  => 5,
                    "reviewCount" => $collection->getSize(),
                ];
            }
        }

        return $data;
    }
}
