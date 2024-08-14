<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\ResourceModel\CartRestore;

use Aheadworks\Acr\Model\CartRestore;
use Aheadworks\Acr\Model\ResourceModel\CartRestore as CartRestoreResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Aheadworks\Acr\Model\ResourceModel\CartRestore
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'entity_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CartRestore::class, CartRestoreResource::class);
    }
}
