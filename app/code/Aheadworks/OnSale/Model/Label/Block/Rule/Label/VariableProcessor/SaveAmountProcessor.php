<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;

use Magento\Framework\Pricing\Helper\Data as PriceHelper;

/**
 * Class SaveAmountProcessor
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor
 */
class SaveAmountProcessor implements VariableProcessorInterface
{
    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var PriceProcessor
     */
    private $productTypePriceProcessor;

    /**
     * @param PriceHelper $priceHelper
     * @param ProductType\PriceProcessor $productTypePriceProcessor
     */
    public function __construct(
        PriceHelper $priceHelper,
        ProductType\PriceProcessor $productTypePriceProcessor
    ) {
        $this->priceHelper = $priceHelper;
        $this->productTypePriceProcessor = $productTypePriceProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process($product, $params)
    {
        $storeId = $product->getStoreId();
        $price = $this->productTypePriceProcessor->prepareRegularPrice($product);
        $specialPrice = $this->productTypePriceProcessor->prepareSpecialPrice($product);

        return ($price && $specialPrice && $price >= $specialPrice)
            ? $this->priceHelper->currencyByStore($price - $specialPrice, $storeId, true, false)
            : '';
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return '$20';
    }
}
