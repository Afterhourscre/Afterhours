<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;

use Magento\Framework\Pricing\Helper\Data as PriceHelper;

/**
 * Class SpecialPriceProcessor
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor
 */
class SpecialPriceProcessor implements VariableProcessorInterface
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
        $specialPrice = $this->productTypePriceProcessor->prepareSpecialPrice($product);

        return $specialPrice
            ? $this->priceHelper->currencyByStore($specialPrice, $storeId, true, false)
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
