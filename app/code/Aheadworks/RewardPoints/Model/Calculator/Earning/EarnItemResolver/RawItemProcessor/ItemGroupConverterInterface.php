<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor;

/**
 * Interface ItemGroupConverterInterface
 * @package Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\RawItemProcessor
 */
interface ItemGroupConverterInterface
{
    /**
     * Convert raw object item groups to item groups
     *
     * @param array $objectItemGroups
     * @return array
     */
    public function convert($objectItemGroups);
}
