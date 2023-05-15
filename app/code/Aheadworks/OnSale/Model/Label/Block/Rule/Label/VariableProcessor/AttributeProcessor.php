<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

/**
 * Class AttributeProcessor
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor
 */
class AttributeProcessor implements VariableProcessorInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductResource $productResource
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductResource $productResource
    ) {
        $this->productRepository = $productRepository;
        $this->productResource = $productResource;
    }

    /**
     * {@inheritdoc}
     */
    public function process($product, $params)
    {
        $attributeCode = $params[0];
        $variableValue = '';
        try {
            /** @var Product $productModel */
            $productModel = $this->productRepository->get($product->getSku());
            if ($this->productResource->getAttribute($attributeCode)) {
                $variableValue = $productModel->getAttributeText($attributeCode);
            }
        } catch (\Exception $e) {
        }

        return $variableValue ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return 'attr value';
    }
}
