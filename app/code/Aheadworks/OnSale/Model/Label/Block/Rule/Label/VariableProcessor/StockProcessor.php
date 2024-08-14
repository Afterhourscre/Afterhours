<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;

use Magento\CatalogInventory\Api\StockStateInterface;

/**
 * Class StockProcessor
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor
 */
class StockProcessor implements VariableProcessorInterface
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
    public function process($product, $params)
    {
        $qty = $this->stockState->getStockQty($product->getId());

        return is_numeric($qty)
            ? $qty
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return '20';
    }
}
