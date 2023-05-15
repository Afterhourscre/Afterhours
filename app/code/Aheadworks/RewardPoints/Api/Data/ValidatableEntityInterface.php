<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Api\Data;

/**
 * Interface ValidatableEntityInterface
 *
 * @package Aheadworks\RewardPoints\Api\Data
 * @api
 */
interface ValidatableEntityInterface
{
    /**
     * Validate entity
     *
     * @return $this
     * @throws \Magento\Framework\Validator\Exception
     */
    public function validate();
}
