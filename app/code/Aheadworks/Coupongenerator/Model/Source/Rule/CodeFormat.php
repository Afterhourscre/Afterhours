<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Source\Rule;

use Magento\SalesRule\Api\Data\CouponGenerationSpecInterface;

class CodeFormat implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => CouponGenerationSpecInterface::COUPON_FORMAT_ALPHANUMERIC,
                'label' => __('Alphanumeric')
            ],
            [
                'value' => CouponGenerationSpecInterface::COUPON_FORMAT_ALPHABETICAL,
                'label' => __('Alphabetical')
            ],
            [
                'value' => CouponGenerationSpecInterface::COUPON_FORMAT_NUMERIC,
                'label' => __('Numeric')
            ]
        ];
    }
}
