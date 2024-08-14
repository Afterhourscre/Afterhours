<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct\RuleProductInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel as MagentoFrameworkAbstractModel;

/**
 * Class Rule
 *
 * @package Aheadworks\OnSale\Model\ResourceModel
 */
class Rule extends AbstractResourceModel
{
    /**#@+
     * Constants defined for table names
     */
    const MAIN_TABLE_NAME = 'aw_onsale_rule';
    const WEBSITE_TABLE_NAME = 'aw_onsale_rule_website';
    const FRONTEND_LABEL_TEXT_TABLE_NAME = 'aw_onsale_rule_frontend_label_text';
    const PRODUCT_TABLE_NAME = 'aw_onsale_rule_product';
    const PRODUCT_IDX_TABLE_NAME = 'aw_onsale_rule_product_idx';
    /**#@-*/

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, RuleInterface::RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function save(MagentoFrameworkAbstractModel $object)
    {
        $object->beforeSave();
        return parent::save($object);
    }

    /**
     * {@inheritdoc}
     */
    public function load(MagentoFrameworkAbstractModel $object, $objectId, $field = null)
    {
        if (!empty($objectId)) {
            $arguments = $this->getArgumentsForEntity();
            $this->entityManager->load($object, $objectId, $arguments);
            $object->afterLoad();
        }
        return $this;
    }

    /**
     * Retrieve sorted rules data by rule priority on few filters
     *
     * @param int $productId
     * @param int $customerGroupId
     * @param int $storeId
     * @param string $currentDate
     * @return array
     */
    public function getSortedRulesDataForProduct($productId, $customerGroupId, $storeId, $currentDate)
    {
        $select = $this->getQueryForSortedRulesForProduct($productId, $customerGroupId, $storeId, $currentDate);

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * Retrieve query for sorted rules by rule priority on few filters from indexer table
     *
     * @param int $productId
     * @param int $customerGroupId
     * @param int $storeId
     * @param string $currentDate
     * @return Select
     */
    public function getQueryForSortedRulesForProduct($productId, $customerGroupId, $storeId, $currentDate)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getTable(self::PRODUCT_TABLE_NAME),
                [
                    RuleProductInterface::LABEL_ID,
                    RuleProductInterface::LABEL_TEXT_LARGE,
                    RuleProductInterface::LABEL_TEXT_MEDIUM,
                    RuleProductInterface::LABEL_TEXT_SMALL,
                    RuleProductInterface::PRIORITY
                ]
            )->where(RuleProductInterface::STORE_ID . ' = ?', $storeId)
            ->where(RuleProductInterface::CUSTOMER_GROUP_ID . ' = ?', $customerGroupId)
            ->where(RuleProductInterface::PRODUCT_ID . ' = ?', $productId)
            ->where(
                'ISNULL(' . RuleProductInterface::FROM_DATE . ') OR ' . RuleProductInterface::FROM_DATE . ' <= ?',
                $currentDate
            )->where(
                'ISNULL(' . RuleProductInterface::TO_DATE . ') OR ' . RuleProductInterface::TO_DATE . ' >= ?',
                $currentDate
            )->order(RuleProductInterface::PRIORITY . ' ASC')
            ->order(RuleProductInterface::LABEL_ID . ' DESC');

        return $select;
    }
}
