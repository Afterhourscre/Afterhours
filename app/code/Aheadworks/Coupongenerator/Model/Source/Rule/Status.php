<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Source\Rule;

/**
 * Class Status
 * @package Aheadworks\Coupongenerator\Model\Source\Rule
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Rule Status values
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::STATUS_ACTIVE,
                'label' => __('Active')
            ],
            [
                'value' => self::STATUS_INACTIVE,
                'label' => __('Inactive')
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
