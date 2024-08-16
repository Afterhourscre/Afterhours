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


namespace Mirasvit\Review\Model\ResourceModel\Review;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mirasvit\Review\Api\Data\ReviewInterface;


class Collection extends \Magento\Review\Model\ResourceModel\Review\Collection
{
    protected function _construct()
    {
        $this->_init(\Mirasvit\Review\Model\Review::class, \Mirasvit\Review\Model\ResourceModel\Review::class);
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->joinLeft(
                ['detail_additional' => $this->getTable(ReviewInterface::DETAIL_ADDITIONAL_TABLE)],
                $this->getJoinConditionExpression('detail_additional'),
                [
                    ReviewInterface::PROS,
                    ReviewInterface::CONS,
                    ReviewInterface::IP,
                    ReviewInterface::COUNTRY,
                    ReviewInterface::COUNTRY_ISO,
                    ReviewInterface::LOCATION,
                    ReviewInterface::PRODUCT_IDS,
                    ReviewInterface::PRODUCT_INFO,
                    ReviewInterface::IS_VERIFIED_BUYER,
                    ReviewInterface::REINDEXED_AT
                ]
            );

        return $this;
    }

    private function getJoinConditionExpression($tableAlias)
    {
        return 'main_table.' . ReviewInterface::ID . ' = ' . $tableAlias . '.' . ReviewInterface::ID;
    }


    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        $group = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);

        if(is_array($group) && in_array('rating.review_id',$group) ) {
            $query = $countSelect->__toString();
            return "SELECT COUNT(*) FROM ($query) Review";
        } else {

            $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

            $part = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);

            if (!is_array($part) || !count($part)) {
                $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));

                return $countSelect;
            }

            $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
            $group = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);

            $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT " . implode(", ", $group) . ")")));
        }

        return $countSelect;
    }

    public function setSortOrder(string $field, string $dir = 'DESC'): self
    {
        if ($field == 'total_rating') {
            $this->getSelect()->joinLeft(['rating' => $this->getResource()->getTable('rating_option_vote')],
                'main_table.review_id = rating.review_id ',
                ['total_rating' => new \Zend_Db_Expr('ROUND(SUM(rating.percent)/COUNT(*)/20)')])
                ->group('rating.review_id');
        }

        $this->setOrder($field, $dir);

        if ($field !== 'created_at') {
            $this->addOrder('created_at', 'desc');
        }

        return $this;
    }
}
