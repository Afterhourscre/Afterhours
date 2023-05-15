<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Source\Label\Shape;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 *
 * @package Aheadworks\OnSale\Model\Source\Label\Shape
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Type values
     */
    const POINT_BURST = 'point_burst';
    const SQUARE = 'square';
    const RECTANGLE = 'rectangle';
    const RECTANGLE_WITH_BEVEL_UP = 'rectangle_with_bevel_up';
    const RECTANGLE_WITH_BEVEL_DOWN = 'rectangle_with_bevel_down';
    const FLAG = 'flag';
    const CIRCLE = 'circle';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::RECTANGLE,
                'label' => __('Rectangle')
            ],
            [
                'value' => self::RECTANGLE_WITH_BEVEL_UP,
                'label' => __('Rectangle with Bevel Up')
            ],
            [
                'value' => self::RECTANGLE_WITH_BEVEL_DOWN,
                'label' => __('Rectangle with Bevel Down')
            ],
            [
                'value' => self::SQUARE,
                'label' => __('Square')
            ],
            [
                'value' => self::CIRCLE,
                'label' => __('Circle')
            ],
            [
                'value' => self::FLAG,
                'label' => __('Flag')
            ],
            [
                'value' => self::POINT_BURST,
                'label' => __('Point Burst')
            ]
        ];
    }
}
