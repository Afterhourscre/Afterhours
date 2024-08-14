<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface PriceInterface
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price
 */
interface PriceInterface
{
    /**
     * Retrieve regular price from product
     *
     * @param ProductInterface $product
     * @return float|int
     */
    public function getRegularPrice($product);

    /**
     * Retrieve special price from product
     *
     * @param ProductInterface $product
     * @return float|int
     */
    public function getSpecialPrice($product);
}
