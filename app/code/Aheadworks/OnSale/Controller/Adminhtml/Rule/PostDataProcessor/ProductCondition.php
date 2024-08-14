<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Rule\PostDataProcessor;

use Aheadworks\OnSale\Controller\Adminhtml\Label\PostDataProcessor\ProcessorInterface;
use Aheadworks\OnSale\Model\Converter\Condition as ConditionConverter;
use Aheadworks\OnSale\Model\Rule\ProductFactory;
use Aheadworks\OnSale\Api\Data\RuleInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class ProductCondition
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule\PostDataProcessor
 */
class ProductCondition implements ProcessorInterface
{
    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @var ProductFactory
     */
    private $productRuleFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ConditionConverter $conditionConverter
     * @param ProductFactory $productRuleFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ConditionConverter $conditionConverter,
        ProductFactory $productRuleFactory,
        SerializerInterface $serializer
    ) {
        $this->conditionConverter = $conditionConverter;
        $this->productRuleFactory = $productRuleFactory;
        $this->serializer = $serializer;
    }

    /**
     * Prepare product conditions for save
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        $data[RuleInterface::PRODUCT_CONDITION] = $this->prepareProductConditionData($data);
        unset($data['rule']);
        return $data;
    }

    /**
     * Prepare product condition data
     *
     * @param array $data
     * @return \Aheadworks\OnSale\Api\Data\ConditionInterface|string
     */
    private function prepareProductConditionData(array $data)
    {
        $productConditionArray = [];
        if (isset($data['rule']['conditions'])) {
            $conditionArray = $this->convertFlatToRecursive($data['rule'], ['conditions']);
            if (is_array($conditionArray['conditions']['1'])) {
                $productConditionArray = $conditionArray['conditions']['1'];
            }
        } else {
            if (isset($data['product_condition'])) {
                $productConditionArray = $this->serializer->unserialize($data['product_condition']);
            } else {
                $productRule = $this->productRuleFactory->create();
                $defaultConditions = [];
                $defaultConditions['rule'] = [];
                $defaultConditions['rule']['conditions'] = $productRule
                    ->setConditions([])
                    ->getConditions()
                    ->asArray();
                $productConditionArray = $this->convertFlatToRecursive($defaultConditions, ['conditions']);
            }
        }
        return $this->conditionConverter->arrayToDataModel($productConditionArray);
    }

    /**
     * Get conditions data recursively
     *
     * @param array $data
     * @param array $allowedKeys
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function convertFlatToRecursive(array $data, $allowedKeys = [])
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedKeys) && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node = & $result;

                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node = & $node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        if (is_array($v)) {
                            foreach ($v as $dk => $dv) {
                                if (empty($dv)) {
                                    unset($v[$dk]);
                                }
                            }
                            if (!count($v)) {
                                continue;
                            }
                        }

                        $node[$k] = $v;
                    }
                }
            }
        }

        return $result;
    }
}
