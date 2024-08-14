<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\ObjectDataProcessors;

use Aheadworks\OnSale\Model\Rule as RuleModel;
use Aheadworks\OnSale\Model\Source\Customer\Group as CustomerGroupSource;

/**
 * Class CustomerGroup
 *
 * @package Aheadworks\OnSale\Model\Rule\ObjectDataProcessors
 */
class CustomerGroup
{
    /**
     * @var CustomerGroupSource
     */
    private $customerGroupSource;

    /**
     * @param CustomerGroupSource $customerGroupSource
     */
    public function __construct(
        CustomerGroupSource $customerGroupSource
    ) {
        $this->customerGroupSource = $customerGroupSource;
    }

    /**
     * Check customer groups data before save and convert it from array to string.
     *
     * @param RuleModel $rule
     * @return RuleModel
     */
    public function beforeSave($rule)
    {
        $customerGroups = $rule->getCustomerGroups();
        if (is_array($customerGroups)) {
            if (in_array(CustomerGroupSource::ALL_GROUPS, $customerGroups)
                || (!is_array($customerGroups) || empty($customerGroups))
            ) {
                $customerGroups = [0 => CustomerGroupSource::ALL_GROUPS];
            }
            $rule->setCustomerGroups(implode(',', $customerGroups));
        }

        return $rule;
    }

    /**
     * Check customer groups data after load
     *
     * @param RuleModel $rule
     * @return RuleModel
     */
    public function afterLoad($rule)
    {
        $customerGroups = $rule->getCustomerGroups();
        if ($customerGroups == CustomerGroupSource::ALL_GROUPS) {
            $allCustomerGroupIds = $this->customerGroupSource->getAllCustomerGroupIds();
            $rule->setCustomerGroups($allCustomerGroupIds);
        } else {
            $rule->setCustomerGroups(explode(',', $customerGroups));
        }

        return $rule;
    }
}
