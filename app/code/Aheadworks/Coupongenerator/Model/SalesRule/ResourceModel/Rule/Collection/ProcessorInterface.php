<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\SalesRule\ResourceModel\Rule\Collection;

use Magento\SalesRule\Model\ResourceModel\Rule\Collection as SalesRuleCollection;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\Coupongenerator\Model\SalesRule\ResourceModel\Rule\Collection
 */
interface ProcessorInterface
{
    /**
     * Update already applied validation filter with CCG-specific conditions
     *
     * @param SalesRuleCollection $collection
     * @param string  $couponCode
     * @return SalesRuleCollection
     */
    public function updateValidationFilter(SalesRuleCollection $collection, $couponCode);
}
