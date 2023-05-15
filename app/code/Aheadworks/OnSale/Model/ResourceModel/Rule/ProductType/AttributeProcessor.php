<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType;

use Magento\Framework\Model\AbstractModel;

/**
 * Class AttributeProcessor
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\ProductType
 */
class AttributeProcessor
{
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
     * Prepare data for each product type.
     * It is also used when doing row index
     *
     * @param string $operation
     * @param AbstractModel $productModel
     * @return int
     */
    public function prepareData($operation, $productModel)
    {
        $result = 0;
        $productType = $productModel->getData(AttributeProcessorInterface::PRODUCT_TYPE_ID);
        $defaultProductType = AttributeProcessorInterface::DEFAULT_PRODUCT_TYPE;

        if (isset($this->processors[$operation][$productType])) {
            $result = $this->processors[$operation][$productType]->process($productModel);
        } elseif (isset($this->processors[$operation][$defaultProductType])) {
            $result = $this->processors[$operation][$defaultProductType]->process($productModel);
        }
        return $result;
    }

    /**
     * Prepare sql selects for each product type when making full reindex
     *
     * @param string $operation
     * @return array
     */
    public function prepareSqlForIndexing($operation)
    {
        $selects = [];
        if (isset($this->processors[$operation])) {
            /** @var AttributeProcessorInterface $productTypeProcessor */
            foreach ($this->processors[$operation] as $productType => $productTypeProcessor) {
                if ($select = $productTypeProcessor->getSelectForIndexing()) {
                    $selects[$productType] = $select;
                }
            }
        }
        return $selects;
    }
}
