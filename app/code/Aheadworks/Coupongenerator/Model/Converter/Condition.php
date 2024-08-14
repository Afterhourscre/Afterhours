<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Converter;

use Magento\SalesRule\Api\Data\ConditionInterfaceFactory;

/**
 * Class Condition
 * @package Aheadworks\Coupongenerator\Model\Converter
 */
class Condition
{
    /**
     * @var ConditionInterfaceFactory
     */
    private $conditionFactory;

    /**
     * @param ConditionInterfaceFactory $conditionFactory
     */
    public function __construct(
        ConditionInterfaceFactory $conditionFactory
    ) {
        $this->conditionFactory = $conditionFactory;
    }

    /**
     * Convert recursive array into condition data model
     *
     * @param array $input
     * @return \Magento\SalesRule\Api\Data\ConditionInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function arrayToDataModel(array $input)
    {
        /** @var \Magento\SalesRule\Api\Data\ConditionInterface $condition */
        $conditionDataModel = $this->conditionFactory->create();
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'type':
                    $conditionDataModel->setConditionType($value);
                    break;
                case 'attribute':
                    $conditionDataModel->setAttributeName($value);
                    break;
                case 'operator':
                    $conditionDataModel->setOperator($value);
                    break;
                case 'value':
                    $conditionDataModel->setValue($value);
                    break;
                case 'aggregator':
                    $conditionDataModel->setAggregatorType($value);
                    break;
                case 'conditions':
                case 'actions':
                    $conditions = [];
                    foreach ($value as $condition) {
                        $conditions[] = $this->arrayToDataModel($condition);
                    }
                    $conditionDataModel->setConditions($conditions);
                    break;
                default:
            }
        }
        return $conditionDataModel;
    }

    /**
     * Convert recursive condition data model into array
     *
     * @param \Magento\SalesRule\Api\Data\ConditionInterface $dataModel
     * @return array
     */
    public function dataModelToArray(\Magento\SalesRule\Api\Data\ConditionInterface $dataModel)
    {
        $output = [
            'type' => $dataModel->getConditionType(),
            'attribute' => $dataModel->getAttributeName(),
            'operator' => $dataModel->getOperator(),
            'value' => $dataModel->getValue(),
            'aggregator' => $dataModel->getAggregatorType()
        ];

        foreach ((array)$dataModel->getConditions() as $conditions) {
            $output['conditions'][] = $this->dataModelToArray($conditions);
        }
        return $output;
    }
}
