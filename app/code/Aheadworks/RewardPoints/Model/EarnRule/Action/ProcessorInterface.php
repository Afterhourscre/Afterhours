<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\EarnRule\Action;

/**
 * Interface ProcessorInterface
 * @package Aheadworks\RewardPoints\Model\EarnRule\Action
 */
interface ProcessorInterface
{
    /**
     * @param float $value
     * @param float $qty
     * @param \Magento\Framework\Api\AttributeInterface[] $attributes
     * @return float
     */
    public function process($value, $qty, $attributes);
}
