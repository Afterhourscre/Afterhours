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

namespace Extait\Cookie\Model\ResourceModel\Cookie;

use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Model;
use Extait\Cookie\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected $_idFieldName = CookieInterface::ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Model\Cookie::class, Model\ResourceModel\Cookie::class);
    }

    public function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['eccs' => $this->getTable('extait_cookie_cookie_store')],
            'main_table.id = eccs.cookie_id',
            '*'
        );
    }

    /**
     * Get Store ID by specific Cookie ID.
     *
     * @param $cookieID
     *
     * @return int
     */
    protected function getStoreID($cookieID)
    {
        $storeID = $this->storeID;

        if ($storeID !== 0) {
            $select = $this->getConnection()->select()
                ->from(['eccs' => $this->getTable('extait_cookie_cookie_store')])
                ->where('eccs.cookie_id = ?', $cookieID)
                ->where('eccs.store_id = ?', $storeID);

            $storeID = count($this->getConnection()->fetchAssoc($select)) ? $storeID : 0;
        }

        return $storeID;
    }
}
