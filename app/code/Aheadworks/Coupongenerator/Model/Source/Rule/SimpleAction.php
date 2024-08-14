<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Source\Rule;

use Magento\SalesRule\Api\Data\RuleInterface;

class SimpleAction implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => RuleInterface::DISCOUNT_ACTION_BY_PERCENT,
                'label' => __('Percent of product price discount')
            ],
            [
                'value' => RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT,
                'label' => __('Fixed amount discount')
            ],
            [
                'value' => RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT_FOR_CART,
                'label' => __('Fixed amount discount for whole cart')
            ],
            [
                'value' => RuleInterface::DISCOUNT_ACTION_BUY_X_GET_Y,
                'label' => __('Buy X get Y free (discount amount is Y)')
            ]
        ];
    }
}
