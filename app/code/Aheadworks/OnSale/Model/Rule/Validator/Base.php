<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\Validator;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\OnSale\Api\Data\RuleInterface;

/**
 * Class Base
 *
 * @package Aheadworks\OnSale\Model\Rule\Validator
 */
class Base extends AbstractValidator
{
    /**
     * Returns true if entity meets the validation requirements
     *
     * @param RuleInterface $rule
     * @return bool
     * @throws \Exception
     */
    public function isValid($rule)
    {
        if (empty($rule->getName())) {
            $this->_addMessages(['Rule name is required.']);
        }

        if (empty($rule->getWebsiteIds())) {
            $this->_addMessages(['Please specify a website.']);
        }

        if (empty($rule->getCustomerGroups()) && $rule->getCustomerGroups() !== '0') {
            $this->_addMessages(['Please specify Customer Groups.']);
        }

        if (empty($rule->getPriority()) && $rule->getPriority() !== '0') {
            $this->_addMessages(['Rule priority is required.']);
        }

        return empty($this->getMessages());
    }
}
