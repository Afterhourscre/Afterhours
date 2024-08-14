<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Hydrator;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Converter as ConditionConverter;
use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Magento\Framework\EntityManager\HydratorInterface;

/**
 * Class Condition
 * @package Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Hydrator
 */
class Condition implements HydratorInterface
{
    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @param ConditionConverter $conditionConverter
     */
    public function __construct(
        ConditionConverter $conditionConverter
    ) {
        $this->conditionConverter = $conditionConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($entity)
    {
        $data = [];
        $condition = $entity->getCondition();
        if ($condition) {
            $data[EarnRuleInterface::CONDITION] = $this->getConditionSerialized($condition);
        }

        return $data;
    }

    /**
     * Get condition serialized
     *
     * @param ConditionInterface $condition
     * @return string
     */
    private function getConditionSerialized($condition)
    {
        $conditionData = $this->conditionConverter->dataModelToArray($condition);

        return serialize($conditionData);
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($entity, array $data)
    {
        if (isset($data[EarnRuleInterface::CONDITION])) {
            /** @var Rule $entity */
            $entity->setCondition($this->getConditionUnserialized($data[EarnRuleInterface::CONDITION]));
        }

        return $entity;
    }

    /**
     * Get unserialized condition
     *
     * @param string $serializedCondition
     * @return ConditionInterface
     */
    private function getConditionUnserialized($serializedCondition)
    {
        $conditionData = unserialize($serializedCondition);

        return $this->conditionConverter->arrayToDataModel($conditionData);
    }
}
