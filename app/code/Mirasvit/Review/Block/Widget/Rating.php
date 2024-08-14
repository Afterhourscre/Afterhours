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


namespace Mirasvit\Review\Block\Widget;


use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Repository\ReviewRepository;

class Rating extends Template implements BlockInterface
{
    protected $_template = 'Mirasvit_Review::widget/rating.phtml';

    private $reviewRepository;

    public function __construct(
        ReviewRepository $reviewRepository,
        Template\Context $context,
        array $data = []
    ) {
        $this->reviewRepository = $reviewRepository;

        parent::__construct($context, $data);
    }

    public function getReviewsCount(int $rating = null): int
    {
        $collection = clone $this->reviewRepository->getCollection()->addStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addStatusFilter(
            ReviewInterface::STATUS_APPROVED
        );

        $collection->getSelect()
            ->joinLeft(['rating' => $collection->getResource()->getTable('rating_option_vote')],
                'main_table.review_id = rating.review_id ',
                ['total_rating' => new \Zend_Db_Expr('ROUND(SUM(rating.percent)/COUNT(*)/20)')])
            ->group('rating.review_id');

        if ($params = $this->getRequest()->getParams()) {
            foreach ($params as $key => $value) {
                switch ($key) {

                    case ReviewInterface::IS_VERIFIED_BUYER :
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

    public function getOverallRating(): float
    {
        $rating = 0;

        $resource = $this->reviewRepository->getCollection()->getResource();

        $select = $resource->getConnection()
            ->select()
            ->from(['rating' => $resource->getTable('rating_option_vote')], ['total_rating' => new \Zend_Db_Expr('SUM(rating.percent)/COUNT(rating.review_id)/20')])
            ->joinRight(
                ['review_store' => $resource->getTable(ReviewInterface::STORE_TABLE)],
                'rating.review_id = review_store.review_id and review_store.store_id = ' . $this->_storeManager->getStore()->getId(),
                null
            );

        return round((float)$resource->getConnection()->fetchOne($select), 2);
    }
}
