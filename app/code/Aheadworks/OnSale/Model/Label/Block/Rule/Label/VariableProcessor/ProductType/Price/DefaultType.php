<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price;

use Magento\Tax\Pricing\Adjustment;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Catalog\Pricing\Price\SpecialPrice;

/**
 * Class DefaultType
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price
 */
class DefaultType implements PriceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRegularPrice($product)
    {
        return $product
            ->getPriceInfo()
            ->getPrice(RegularPrice::PRICE_CODE)
            ->getAmount()
            ->getValue(Adjustment::ADJUSTMENT_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialPrice($product)
    {
        return $product
            ->getPriceInfo()
            ->getPrice(SpecialPrice::PRICE_CODE)
            ->getAmount()
            ->getValue(Adjustment::ADJUSTMENT_CODE);
    }
}
