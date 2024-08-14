<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\ResourceModel\Rule;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Model\Rule;
use Aheadworks\Acr\Model\ResourceModel\Rule as RuleResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Aheadworks\Acr\Model\ResourceModel\Rule
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Rule::class, RuleResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->processItems();
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreLinkageTable();
        parent::_renderFiltersBefore();
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_ids') {
            $this->addFilter('store_id', ['in' => $condition], 'public');
            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Attach stores to collection items
     *
     * @return void
     */
    private function processItems()
    {
        $ruleIds = $this->getColumnValues('id');
        if (count($ruleIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['store_linkage_table' => $this->getTable('aw_acr_rule_store')])
                ->where('store_linkage_table.rule_id IN (?)', $ruleIds);
            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $stores = [];
                $ruleId = $item->getData('id');
                foreach ($connection->fetchAll($select) as $data) {
                    if ($data['rule_id'] == $ruleId) {
                        $stores[] = $data['store_id'];
                    }
                }
                $item->setData(RuleInterface::STORE_IDS, $stores);
                if (!is_array($item->getData(RuleInterface::CUSTOMER_GROUPS))) {
                    $item->setData(
                        RuleInterface::CUSTOMER_GROUPS,
                        explode(',', $item->getData(RuleInterface::CUSTOMER_GROUPS))
                    );
                }
                if (!is_array($item->getData(RuleInterface::PRODUCT_TYPE_IDS))) {
                    $item->setData(
                        RuleInterface::PRODUCT_TYPE_IDS,
                        explode(',', $item->getData(RuleInterface::PRODUCT_TYPE_IDS))
                    );
                }
            }
        }
    }

    /**
     * Join to store linkage table if store filter is applied
     *
     * @return void
     */
    private function joinStoreLinkageTable()
    {
        if ($this->getFilter('store_id')) {
            $select = $this->getSelect();
            $select->joinLeft(
                ['store_linkage_table' => $this->getTable('aw_acr_rule_store')],
                'main_table.id = store_linkage_table.rule_id',
                []
            )
                ->group('main_table.id');
        }
    }
}
