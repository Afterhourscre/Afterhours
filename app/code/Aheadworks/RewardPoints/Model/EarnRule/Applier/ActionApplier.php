<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\EarnRule\Applier;

use Aheadworks\RewardPoints\Api\Data\ActionInterface;
use Aheadworks\RewardPoints\Model\EarnRule\Action\Type as ActionType;
use Aheadworks\RewardPoints\Model\EarnRule\Action\TypePool as ActionTypePool;

/**
 * Class ActionApplier
 * @package Aheadworks\RewardPoints\Model\EarnRule\Applier
 */
class ActionApplier
{
    /**
     * @var ActionTypePool
     */
    private $actionTypePool;

    /**
     * @param ActionTypePool $actionTypePool
     */
    public function __construct(
        ActionTypePool $actionTypePool
    ) {
        $this->actionTypePool = $actionTypePool;
    }

    /**
     * Apply action
     *
     * @param float $points
     * @param float $qty
     * @param ActionInterface $action
     * @return float
     */
    public function apply($points, $qty, $action)
    {
        try {
            /** @var ActionType $actionType */
            $actionType = $this->actionTypePool->getTypeByCode($action->getType());
            $processor = $actionType->getProcessor();

            $points = $processor->process($points, $qty, $action->getAttributes());
        } catch (\Exception $e) {
        }

        return $points;
    }
}
