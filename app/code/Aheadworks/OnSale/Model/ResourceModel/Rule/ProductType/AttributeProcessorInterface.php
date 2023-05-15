<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType;

use Magento\Framework\Model\AbstractModel;

/**
 * Interface AttributeProcessorInterface
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType
 */
interface AttributeProcessorInterface
{
    /**#@+
     * Constants defined for product entity
     */
    const PRODUCT_ENTITY_ID = 'entity_id';
    const PRODUCT_TYPE_ID = 'type_id';
    /**#@-*/

    /**
     * Default product type.
     * It is used when other product type cannot be found
     */
    const DEFAULT_PRODUCT_TYPE = 'default';

    /**
     * Process the counting of some product data
     *
     * @param AbstractModel $product
     * @return mixed
     */
    public function process($product);

    /**
     * Prepare SQL select when indexing data
     *
     * @return mixed
     */
    public function getSelectForIndexing();
}
