<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Hydrator;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\Action\Converter as ActionConverter;
use Aheadworks\RewardPoints\Api\Data\ActionInterface;
use Magento\Framework\EntityManager\HydratorInterface;

/**
 * Class Action
 * @package Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Hydrator
 */
class Action implements HydratorInterface
{
    /**
     * @var ActionConverter
     */
    private $actionConverter;

    /**
     * @param ActionConverter $actionConverter
     */
    public function __construct(
        ActionConverter $actionConverter
    ) {
        $this->actionConverter = $actionConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($entity)
    {
        $data = [];
        $action = $entity->getAction();
        if ($action) {
            $data[EarnRuleInterface::ACTION] = $this->getActionSerialized($action);
        }

        return $data;
    }

    /**
     * Get action serialized
     *
     * @param ActionInterface $action
     * @return string
     */
    private function getActionSerialized($action)
    {
        $actionData = $this->actionConverter->dataModelToArray($action);

        return serialize($actionData);
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate($entity, array $data)
    {
        if (isset($data[EarnRuleInterface::ACTION])) {
            /** @var EarnRuleInterface $entity */
            $entity->setAction($this->getActionUnserialized($data[EarnRuleInterface::ACTION]));
        }

        return $entity;
    }

    /**
     * Get unserialized action
     *
     * @param string $serializedAction
     * @return ActionInterface
     */
    private function getActionUnserialized($serializedAction)
    {
        $actionData = unserialize($serializedAction);

        return $this->actionConverter->arrayToDataModel($actionData);
    }
}
