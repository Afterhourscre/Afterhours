<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Source\Label;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 *
 * @package Aheadworks\OnSale\Model\Source\Label
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Type values
     */
    const SHAPE = 'shape';
    const PICTURE = 'picture';
    const TEXT = 'text';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SHAPE,
                'label' => __('Shape')
            ],
            [
                'value' => self::PICTURE,
                'label' => __('Picture')
            ],
            [
                'value' => self::TEXT,
                'label' => __('Text')
            ]
        ];
    }
}
