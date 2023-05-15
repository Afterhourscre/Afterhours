<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;

/**
 * Class SavePercentProcessor
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor
 */
class SavePercentProcessor implements VariableProcessorInterface
{
    /**
     * @var PriceProcessor
     */
    private $productTypePriceProcessor;

    /**
     * @param ProductType\PriceProcessor $productTypePriceProcessor
     */
    public function __construct(
        ProductType\PriceProcessor $productTypePriceProcessor
    ) {
        $this->productTypePriceProcessor = $productTypePriceProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process($product, $params)
    {
        $price = $this->productTypePriceProcessor->prepareRegularPrice($product);
        $specialPrice = $this->productTypePriceProcessor->prepareSpecialPrice($product);

        return ($price && $specialPrice && $price >= $specialPrice)
            ? $this->calculatePercent($price, $specialPrice) . '%'
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return '20%';
    }

    /**
     * Calculate percent difference between price and special price
     *
     * @param float|int $price
     * @param float|int $specialPrice
     * @return float|int
     */
    private function calculatePercent($price, $specialPrice)
    {
        return round(100 - $specialPrice * 100 / $price);
    }
}
