<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Cookie
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\Cookie\Model\ResourceModel\Category;

use Extait\Cookie\Model;
use Extait\Cookie\Api\Data\CategoryInterface;
use Extait\Cookie\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected $_idFieldName = CategoryInterface::ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Model\Category::class, Model\ResourceModel\Category::class);
    }

    /**
     * Add additional data from store table.
     *
     * @return \Extait\Cookie\Model\ResourceModel\AbstractCollection
     */
    public function _afterLoadData()
    {
        foreach ($this->getData() as $key => $categoryData) {
            $select = $this->getConnection()->select()
                ->from(['eccs' => 'extait_cookie_category_store'])
                ->where('eccs.store_id = ?', $this->getStoreID($categoryData['id']))
                ->where('eccs.category_id = ?', $categoryData['id']);

            $this->_data[$key] = $categoryData + $this->getConnection()->fetchRow($select);
        }

        return parent::_afterLoadData();
    }

    /**
     * Get Store ID by specific Category ID.
     *
     * @param $categoryID
     * @return int
     */
    protected function getStoreID($categoryID)
    {
        $storeID = $this->storeID;

        if ($storeID !== 0) {
            $select = $this->getConnection()->select()
                ->from(['eccs' => $this->getTable('extait_cookie_category_store')])
                ->where('eccs.category_id = ?', $categoryID)
                ->where('eccs.store_id = ?', $storeID);

            $storeID = count($this->getConnection()->fetchAssoc($select)) ? $storeID : 0;
        }

        return $storeID;
    }
}
