<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;

/**
 * Class Configurable
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price
 */
class Configurable extends DefaultType implements PriceInterface
{
    /**
     * @var LowestPriceOptionsProviderInterface
     */
    private $lowestPriceOptionsProvider;

    /**
     * @param LowestPriceOptionsProviderInterface $lowestPriceOptionsProvider
     */
    public function __construct(
        LowestPriceOptionsProviderInterface $lowestPriceOptionsProvider
    ) {
        $this->lowestPriceOptionsProvider = $lowestPriceOptionsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegularPrice($product)
    {
        $minAmount = 0;
        foreach ($this->getLowestPriceChildProducts($product) as $lowestPriceProduct) {
            $childPriceAmount = parent::getRegularPrice($lowestPriceProduct);
            if (!$minAmount || ($childPriceAmount < $minAmount)) {
                $minAmount = $childPriceAmount;
            }
        }
        return $minAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialPrice($product)
    {
        $minAmount = 0;
        foreach ($this->getLowestPriceChildProducts($product) as $lowestPriceProduct) {
            $childPriceAmount = parent::getSpecialPrice($lowestPriceProduct);
            if (!$minAmount || ($childPriceAmount < $minAmount)) {
                $minAmount = $childPriceAmount;
            }
        }
        return $minAmount;
    }

    /**
     * Get children of configurable product with lowest price
     *
     * @param ProductInterface $product
     * @return ProductInterface[]
     */
    private function getLowestPriceChildProducts($product)
    {
        return $this->lowestPriceOptionsProvider->getProducts($product);
    }
}
