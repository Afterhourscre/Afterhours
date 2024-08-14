<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\Operation\Stock;

use Magento\CatalogInventory\Api\StockStateInterface;
use Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\AttributeProcessorInterface;

/**
 * Class DefaultType
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType\Operation\Stock
 */
class DefaultType implements AttributeProcessorInterface
{
    /**
     * @var StockStateInterface
     */
    private $stockState;

    /**
     * @param StockStateInterface $stockState
     */
    public function __construct(
        StockStateInterface $stockState
    ) {
        $this->stockState = $stockState;
    }

    /**
     * {@inheritdoc}
     */
    public function process($product)
    {
        $productId = $product->getData(AttributeProcessorInterface::PRODUCT_ENTITY_ID);
        return $this->stockState->getStockQty($productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectForIndexing()
    {
        return false;
    }
}
