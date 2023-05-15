<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Source\Coupon;

/**
 * Class Status
 * @package Aheadworks\Coupongenerator\Model\Source\Coupon
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Coupon status values
     */
    const AVAILABLE_VALUE       = 1;
    const EXPIRED_VALUE         = 2;
    const USED_VALUE            = 3;
    const DEACTIVATED_VALUE     = 4;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::AVAILABLE_VALUE,
                'label' => __('Active')
            ],
            [
                'value' => self::EXPIRED_VALUE,
                'label' => __('Expired')
            ],
            [
                'value' => self::USED_VALUE,
                'label' => __('Used')
            ],
            [
                'value' => self::DEACTIVATED_VALUE,
                'label' => __('Deactivated')
            ]
        ];
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->toOptionArray();
        $result = [];

        foreach ($options as $option) {
            $result[$option['value']] = $option['label'];
        }

        return $result;
    }

    /**
     * Get option by value
     *
     * @param int $value
     * @return null
     */
    public function getOptionByValue($value)
    {
        $options = $this->getOptions();
        if (array_key_exists($value, $options)) {
            return $options[$value];
        }
        return null;
    }
}
