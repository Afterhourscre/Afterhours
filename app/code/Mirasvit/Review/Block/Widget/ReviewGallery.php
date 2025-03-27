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

use Magento\Widget\Block\BlockInterface;
use Mirasvit\Review\Api\Data\ReviewInterface;
use Mirasvit\Review\Api\Data\ReviewMediaInterface;
use Mirasvit\Review\Block\Product\Review\Gallery;
use Mirasvit\Review\Model\ResourceModel\ReviewMedia\Collection as MediaCollection;

class ReviewGallery extends Gallery implements BlockInterface
{
    public function getImages(): ?array
    {
        $collection = $this->mediaRepository->getCollection()
            ->addFieldToFilter(ReviewMediaInterface::TYPE, 'image');

        $reviewTable = $collection->getTable(ReviewInterface::MAIN_TABLE);
        $collection
            ->getSelect()
            ->joinLeft(
                $reviewTable,
                'main_table.' . ReviewMediaInterface::REVIEW_ID . ' = ' . $reviewTable . '.' . ReviewInterface::ID ,
                ReviewInterface::STATUS_ID
            );

        $collection->addFieldToFilter( ReviewInterface::STATUS_ID, ReviewInterface::STATUS_APPROVED);

        $this->setOrder($collection);
        $this->setLimit($collection);

        return $collection->getItems();
    }

    private function setOrder(MediaCollection $collection)
    {
        $sortOrder = (string)$this->getData('sort_order');

        list($field, $dir) = explode('-', $sortOrder);

        if (!$field) {
            $field = 'created_at';
        }

        if ($field == 'total_rating') {
            $collection->getSelect()->joinLeft(['rating' => $collection->getResource()->getTable('rating_option_vote')],
                'main_table.review_id = rating.review_id ',
                ['total_rating' => new \Zend_Db_Expr('ROUND(SUM(rating.percent)/COUNT(*)/20)')])
                ->group('rating.review_id');
        }

        $collection->setOrder($field, $dir ?: 'desc');

        if ($field !== 'created_at') {
            $collection->addOrder('created_at', 'desc');
        }

        $collection->setOrder('main_table.' . ReviewMediaInterface::REVIEW_ID, 'DESC')
            ->setOrder('main_table.' . ReviewMediaInterface::MEDIA_ID, 'ASC');
    }

    private function setLimit(MediaCollection $collection)
    {
        $limit = $this->getData('limit') ?: 1000;

        if ($limit > 1000) {
            $limit = 1000;
        }

        if ($limit < 0) {
            $limit = 0;
        }

        $collection->setPageSize($limit);
    }
}
