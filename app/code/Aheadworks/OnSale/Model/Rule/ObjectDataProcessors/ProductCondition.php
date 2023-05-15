<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\ObjectDataProcessors;

use Aheadworks\OnSale\Model\Rule as RuleModel;
use Aheadworks\OnSale\Model\Converter\Condition as ConditionConverter;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class ProductCondition
 *
 * @package Aheadworks\OnSale\Model\Rule\ObjectDataProcessors
 */
class ProductCondition
{
    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ConditionConverter $conditionConverter
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ConditionConverter $conditionConverter,
        SerializerInterface $serializer
    ) {
        $this->conditionConverter = $conditionConverter;
        $this->serializer = $serializer;
    }

    /**
     * Check product conditions data before save
     *
     * @param RuleModel $rule
     * @return RuleModel
     */
    public function beforeSave($rule)
    {
        if (is_object($rule->getProductCondition())) {
            $productConditionDataModel = $rule->getProductCondition();
            $productConditionArray = $this->conditionConverter->dataModelToArray($productConditionDataModel);
            $rule->setProductCondition($this->serializer->serialize($productConditionArray));
        }

        return $rule;
    }

    /**
     * Check product conditions data after load
     *
     * @param RuleModel $rule
     * @return RuleModel
     */
    public function afterLoad($rule)
    {
        if ($rule->getProductCondition()) {
            $productConditionArray = $this->serializer->unserialize($rule->getProductCondition());
            $productConditionDataModel = $this->conditionConverter->arrayToDataModel($productConditionArray);
            $rule->setProductCondition($productConditionDataModel);
        }

        return $rule;
    }
}
