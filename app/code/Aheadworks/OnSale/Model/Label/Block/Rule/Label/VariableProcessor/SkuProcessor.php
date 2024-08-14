<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;

/**
 * Class SkuProcessor
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor
 */
class SkuProcessor implements VariableProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($product, $params)
    {
        $sku = $product->getSku();
        return $sku ? $sku : '';
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return 'sku';
    }
}
