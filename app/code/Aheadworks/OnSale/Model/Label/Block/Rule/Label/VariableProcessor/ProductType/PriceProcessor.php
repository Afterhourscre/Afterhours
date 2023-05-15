<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Bundle\Model\Product\Type as Bundle;

/**
 * Class PriceProcessor
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType
 */
class PriceProcessor
{
    /**
     * Default product type.
     * It is used in case other product types are not found
     */
    const DEFAULT_PRODUCT_TYPE = 'default';

    /**
     * @var array[]
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Can render price for product type
     *
     * @param string $productType
     * @return bool
     */
    private function canRenderPrice($productType)
    {
        return (
            $productType != Grouped::TYPE_CODE
            && $productType != Bundle::TYPE_CODE
        );
    }

    /**
     * Prepare regular price
     *
     * @param ProductInterface $product
     * @return mixed
     */
    public function prepareRegularPrice($product)
    {
        $price = 0;
        $productType = $product->getTypeId();

        if (!$this->canRenderPrice($productType)) {
            return $price;
        }
        if (isset($this->processors[$productType])) {
            $price = $this->processors[$productType]->getRegularPrice($product);
        } elseif (isset($this->processors[self::DEFAULT_PRODUCT_TYPE])) {
            $price = $this->processors[self::DEFAULT_PRODUCT_TYPE]->getRegularPrice($product);
        }

        return $price;
    }

    /**
     * Prepare special price
     *
     * @param ProductInterface $product
     * @return mixed
     */
    public function prepareSpecialPrice($product)
    {
        $price = 0;
        $productType = $product->getTypeId();

        if (!$this->canRenderPrice($productType)) {
            return $price;
        }
        if (isset($this->processors[$productType])) {
            $price = $this->processors[$productType]->getSpecialPrice($product);
        } elseif (isset($this->processors[self::DEFAULT_PRODUCT_TYPE])) {
            $price = $this->processors[self::DEFAULT_PRODUCT_TYPE]->getSpecialPrice($product);
        }

        return $price;
    }
}
