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

/**
 * Abstract collection for cookie collections.
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var int
     */
    protected $storeID = 0;

    /**
     * Get Store ID by specific entity ID.
     *
     * @param $entityID
     * @return mixed
     */
    abstract protected function getStoreID($entityID);

    /**
     * Set Store ID to collection.
     *
     * @param $storeID
     */
    public function setStoreID($storeID)
    {
        $this->storeID = $storeID;
    }
}
