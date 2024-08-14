<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Source\Label\Text;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Variable
 *
 * @package Aheadworks\OnSale\Model\Source\Label\Text
 */
class Variable implements OptionSourceInterface
{
    /**#@+
     * Label text variable constants
     */
    const ATTRIBUTE = 'attribute:code';
    const SAVE_PERCENT = 'save_percent';
    const SAVE_AMOUNT = 'save_amount';
    const PRICE = 'price';
    const SPECIAL_PRICE = 'special_price';
    const STOCK = 'stock';
    const BR = 'br';
    const SKU = 'sku';
    const SPDL = 'spdl';
    const SPHL = 'sphl';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ATTRIBUTE, 'label' => __('attribute value')],
            ['value' => self::SAVE_PERCENT, 'label' => __('discount percentage')],
            ['value' => self::SAVE_AMOUNT, 'label' => __('discount amount')],
            ['value' => self::PRICE, 'label' => __('regular price')],
            ['value' => self::SPECIAL_PRICE, 'label' => __('special price')],
            ['value' => self::STOCK, 'label' => __('stock amount')],
            ['value' => self::BR, 'label' => __('new line')],
            ['value' => self::SKU, 'label' => __('product SKU')],
            ['value' => self::SPDL, 'label' => __('X days left for special price')],
            ['value' => self::SPHL, 'label' => __('X hours left for special price')]
        ];
    }

    /**
     * Retrieve variables available in testarea
     *
     * @return array
     */
    public function getVariablesAvailableInTestArea()
    {
        $testOptions = [];
        $testVariables = [self::BR];
        foreach ($this->toOptionArray() as $option) {
            if (in_array($option['value'], $testVariables)) {
                $testOptions[] = $option;
            }
        }

        return $testOptions;
    }

    /**
     * Return array of options as value-label pairs as variable description
     *
     * @param array $options
     * @return array
     */
    public function getOptionsAsVariableDescription($options)
    {
        foreach ($options as &$option) {
            $option['value'] = '{' . $option['value'] . '}';
        }

        return $options;
    }
}
