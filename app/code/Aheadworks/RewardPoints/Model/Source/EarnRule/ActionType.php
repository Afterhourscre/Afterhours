<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\Source\EarnRule;

use Aheadworks\RewardPoints\Model\EarnRule\Action\TypePool as ActionTypePool;
use Aheadworks\RewardPoints\Model\EarnRule\Action\TypeInterface as ActionTypeInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ActionType
 * @package Aheadworks\RewardPoints\Model\Source\EarnRule
 */
class ActionType implements OptionSourceInterface
{
    /**
     * @var ActionTypePool
     */
    private $actionTypePool;

    /**
     * @var array
     */
    private $options;

    /**
     * @param ActionTypePool $actionTypePool
     */
    public function __construct(
        ActionTypePool $actionTypePool
    ) {
        $this->actionTypePool = $actionTypePool;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [];
            try {
                /** @var ActionTypeInterface $type */
                foreach ($this->actionTypePool->getTypes() as $code => $type) {
                    $this->options[] = [
                        'value' => $code,
                        'label' => __($type->getTitle())
                    ];
                }
            } catch (\Exception $e) {
            }
        }
        return $this->options;
    }
}
