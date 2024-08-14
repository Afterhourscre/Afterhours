<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Api\Data;

/**
 * Label text store value interface
 * @api
 */
interface LabelTextStoreValueInterface extends LabelTextInterface
{
    /**
     * Store ID
     */
    const STORE_ID = 'store_id';

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);
}
