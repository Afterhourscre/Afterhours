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

namespace Extait\Cookie\Model\ResourceModel;

use Extait\Cookie\Api\Data\CookieInterface;
use Magento\Framework\Model\AbstractModel;

class Cookie extends AbstractResourceModel
{
    /**
     * The name of main table.
     */
    const MAIN_TABLE = 'extait_cookie_cookie';
    const STORE_TABLE = 'extait_cookie_cookie_store';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, CookieInterface::ID);
    }

    /**
     * Get the cookie ID by name.
     *
     * @param string $name
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIdByName($name)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from($this->getMainTable(), CookieInterface::ID)->where('name = :name');

        return $connection->fetchOne($select, [':name' => (string)$name]);
    }

    /**
     * Save data to a specific store in the database.
     *
     * @param \Magento\Framework\Model\AbstractModel|\Extait\Cookie\Api\Data\CookieInterface $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->getConnection()->insertOnDuplicate(
            self::STORE_TABLE,
            [
                'cookie_id' => $object->getId(),
                'store_id' => $object->getData('store_id') ?: 0,
                'description' => $object->getDescription(),
            ]
        );

        return parent::_afterSave($object);
    }

    /**
     * Add additional Data from store table.
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $storeID = $this->getStoreID($value, $object);

        $select->joinLeft(
            ['eccs' => $this->getTable('extait_cookie_cookie_store')],
            'extait_cookie_cookie.id = eccs.cookie_id',
            ['description']
        )->where('eccs.store_id = ?', $storeID);

        return $select;
    }

    /**
     * Get Store ID.
     *
     * @param $categoryID
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int|mixed
     */
    private function getStoreID($categoryID, AbstractModel $object)
    {
        $storeID = $object->getData('store_id') ? $object->getData('store_id') : $this->request->getParam('store', 0);

        if ($storeID !== 0) {
            $select = $this->getConnection()->select()
                ->from(['eccs' => $this->getTable('extait_cookie_cookie_store')])
                ->where('eccs.cookie_id = ?', $categoryID)
                ->where('eccs.store_id = ?', $storeID);

            $storeID = count($this->getConnection()->fetchAssoc($select)) ? $storeID : 0;
        }

        return $storeID;
    }
}
