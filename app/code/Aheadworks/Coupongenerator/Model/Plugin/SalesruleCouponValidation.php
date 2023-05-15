<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Plugin;

use Aheadworks\Coupongenerator\Model\SalesRule\ResourceModel\Rule\Collection\Processor;
use Magento\Framework\Module\Manager;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection as RuleCollection;
use Magento\Quote\Model\Quote\Address;

/**
 * Class SalesruleCouponValidation
 * @package Aheadworks\Coupongenerator\Model\Plugin
 * @codeCoverageIgnore
 */
class SalesruleCouponValidation
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @param Manager $moduleManager
     * @param Processor $processor
     */
    public function __construct(
        Manager $moduleManager,
        Processor $processor
    ) {
        $this->moduleManager = $moduleManager;
        $this->processor = $processor;
    }

    /**
     * Update rule collection by coupongenerator conditions
     *
     * @param RuleCollection $subject
     * @param \Closure $proceed
     * @param int $websiteId
     * @param int $customerGroupId
     * @param string $couponCode
     * @param null $now
     * @param Address|null $address
     * @return RuleCollection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSetValidationFilter(
        RuleCollection $subject,
        \Closure $proceed,
        $websiteId,
        $customerGroupId,
        $couponCode = '',
        $now = null,
        Address $address = null
    ) {
        /** @var RuleCollection $ruleCollection */
        $ruleCollection = $proceed($websiteId, $customerGroupId, $couponCode, $now, $address);

        if ($this->moduleManager->isOutputEnabled('Aheadworks_Coupongenerator')) {
            $this->processor->updateValidationFilter($ruleCollection, $couponCode);
        }

        return $ruleCollection;
    }
}
