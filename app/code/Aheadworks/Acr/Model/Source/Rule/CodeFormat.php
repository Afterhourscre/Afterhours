<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Source\Rule;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterface;

/**
 * Class CodeFormat
 * @package Aheadworks\Acr\Model\Source\Rule
 */
class CodeFormat implements OptionSourceInterface
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
