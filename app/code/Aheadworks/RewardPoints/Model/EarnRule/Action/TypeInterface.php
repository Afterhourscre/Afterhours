<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\EarnRule\Action;

/**
 * Interface TypeInterface
 * @package Aheadworks\RewardPoints\Model\EarnRule\Action
 */
interface TypeInterface
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get processor
     *
     * @return ProcessorInterface
     */
    public function getProcessor();

    /**
     * Get attribute codes
     *
     * @return string[]
     */
    public function getAttributeCodes();
}
