<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;

use Magento\Framework\Pricing\Helper\Data as PriceHelper;

/**
 * Class PriceProcessor
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor
 */
class PriceProcessor implements VariableProcessorInterface
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

        return $price
            ? $this->priceHelper->currencyByStore($price, $storeId, true, false)
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
