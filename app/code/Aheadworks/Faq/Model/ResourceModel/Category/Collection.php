<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model\ResourceModel\Category;

use Aheadworks\Faq\Model\Category;
use Aheadworks\Faq\Model\ResourceModel\Category as CategoryResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as MagentoAbstractCollection;
use Magento\Store\Model\Store;

class Collection extends MagentoAbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'category_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Category::class, CategoryResource::class);
        $this->_map['fields']['category_id'] = 'main_table.category_id';
        $this->_map['fields']['store'] = 'store_table.store_ids';
    }

    /**
     * Returns pairs identifier - title for unique identifiers
     * and pairs url-key|category_id - title for non-unique after first
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $result = [];
        $existingIdentifiers = [];
        foreach ($this as $item) {
            $urlKey = $item->getData('url_key');

            $data['value'] = $urlKey;
            $data['label'] = $item->getData('name');

            if (in_array($urlKey, $existingIdentifiers)) {
                $data['value'] .= '|' . $item->getData('category_id');
            } else {
                $existingIdentifiers[] = $urlKey;
            }

            $result[] = $data;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachStore();
        return parent::_afterLoad();
    }

    /**
     * Attach store to collection items
     *
     * @return void
     */
    protected function attachStore()
    {
        $linkField = 'category_id';

        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['faq' => $this->getTable('aw_faq_category_store')])
                ->where('faq.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $resultData = [];
                foreach ($result as $resultItem) {
                    $resultData[$resultItem[$linkField]][] = $resultItem['store_ids'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($resultData[$linkedId])) {
                        continue;
                    }
                    $item->setData('store_id', $resultData[$linkedId]);
                    $item->setData('store_ids', $resultData[$linkedId]);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable();
        parent::_renderFiltersBefore();
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_ids') {
            return $this->addStoreFilter($condition, false);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add store filter to collection
     *
     * @param int|array|Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $storeToFilter = [];
        if (!is_array($store)) {
            if ($store instanceof Store) {
                $storeToFilter[] = $store->getId();
            } else {
                $storeToFilter[] = $store;
            }
        } else {
            $storeToFilter = $store;
        }

        if ($withAdmin) {
            $storeToFilter[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', ['in' => $storeToFilter], 'public');

        return $this;
    }

    /**
     * Join store relation table
     *
     * @return void
     */
    protected function joinStoreRelationTable()
    {
        if ($this->getFilter('store')) {
            $this
                ->getSelect()
                ->join(
                    ['store_table' => $this->getTable('aw_faq_category_store')],
                    'main_table.category_id = store_table.category_id',
                    []
                )
                ->group(
                    'main_table.category_id'
                );
        }
    }
}
