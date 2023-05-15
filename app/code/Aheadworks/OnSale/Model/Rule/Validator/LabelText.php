<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Rule\Validator;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;

/**
 * Class LabelText
 *
 * @package Aheadworks\OnSale\Model\Rule\Validator
 */
class LabelText extends AbstractValidator
{
    /**
     * Returns true in case label text data is correct
     *
     * @param RuleInterface $rule
     * @return bool
     * @throws \Exception
     */
    public function isValid($rule)
    {
        $labelTextStoreIds = [];
        if ($rule->getFrontendLabelTextStoreValues() && (is_array($rule->getFrontendLabelTextStoreValues()))) {
            /** @var LabelTextStoreValueInterface $labelTextStoreValue */
            foreach ($rule->getFrontendLabelTextStoreValues() as $labelTextStoreValue) {
                if (!in_array($labelTextStoreValue->getStoreId(), $labelTextStoreIds)) {
                    array_push($labelTextStoreIds, $labelTextStoreValue->getStoreId());
                } else {
                    $this->_addMessages(['Duplicated store view in frontend label text found.']);
                }
            }
        }
        return empty($this->getMessages());
    }
}
