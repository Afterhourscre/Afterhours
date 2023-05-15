<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\Validator;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\OnSale\Api\Data\RuleInterface;

/**
 * Class Date
 *
 * @package Aheadworks\OnSale\Model\Rule\Validator
 */
class Date extends AbstractValidator
{
    /**
     * Returns true from date is less then to date
     *
     * @param RuleInterface $rule
     * @return bool
     * @throws \Exception
     */
    public function isValid($rule)
    {
        $fromDate = $rule->getFromDate();
        $toDate = $rule->getToDate();

        if ($fromDate && $toDate) {
            $fromDate = new \DateTime($fromDate);
            $toDate = new \DateTime($toDate);

            if ($fromDate > $toDate) {
                $this->_addMessages(['End Date must follow Start Date.']);
            }
        }

        return empty($this->getMessages());
    }
}
