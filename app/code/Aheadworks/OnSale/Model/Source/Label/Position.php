<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Source\Label;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Position
 *
 * @package Aheadworks\OnSale\Model\Source\Label
 */
class Position implements OptionSourceInterface
{
    /**#@+
     * Position values
     */
    const TOP_LEFT = 'top_left';
    const TOP_RIGHT = 'top_right';
    const BOTTOM_RIGHT = 'bottom_right';
    const BOTTOM_LEFT = 'bottom_left';
    const NEXT_TO_PRICE = 'next_to_price';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::TOP_LEFT,
                'label' => __('Upper Left')
            ],
            [
                'value' => self::TOP_RIGHT,
                'label' => __('Upper Right')
            ],
            [
                'value' => self::BOTTOM_RIGHT,
                'label' => __('Lower Right')
            ],
            [
                'value' => self::BOTTOM_LEFT,
                'label' => __('Lower Left')
            ],
            [
                'value' => self::NEXT_TO_PRICE,
                'label' => __('Next To Price')
            ]
        ];
    }

    /**
     * Retrieve position by classes map
     *
     * @return array
     */
    public function getPositionClassesMap()
    {
        return [
            Position::TOP_LEFT => 'top-left',
            Position::TOP_RIGHT => 'top-right',
            Position::BOTTOM_LEFT => 'bottom-left',
            Position::BOTTOM_RIGHT => 'bottom-right',
            Position::NEXT_TO_PRICE => 'next-to-price'
        ];
    }

    /**
     * Retrieve positions for invert labels
     *
     * @return array
     */
    public function getInvertLabelPositions()
    {
        return [
            Position::BOTTOM_LEFT => 'bottom-left',
            Position::BOTTOM_RIGHT => 'bottom-right'
        ];
    }

    /**
     * Retrieve class by position
     *
     * @param string $position
     * @return string
     */
    public function getClassByPosition($position)
    {
        return $this->getPositionClassesMap()[$position];
    }

    /**
     * Check if label needs to invert
     *
     * @param string $position
     * @return bool
     */
    public function isInvertLabelByPosition($position)
    {
        return isset($this->getInvertLabelPositions()[$position]);
    }
}
