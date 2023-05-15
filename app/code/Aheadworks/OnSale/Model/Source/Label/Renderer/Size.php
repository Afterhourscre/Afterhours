<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Source\Label\Renderer;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Size
 *
 * @package Aheadworks\OnSale\Model\Source\Label\Renderer
 */
class Size implements OptionSourceInterface
{
    /**#@+
     * Type values
     */
    const LARGE = 'large';
    const MEDIUM = 'medium';
    const SMALL = 'small';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::LARGE,
                'label' => __('Large')
            ],
            [
                'value' => self::MEDIUM,
                'label' => __('Medium')
            ],
            [
                'value' => self::SMALL,
                'label' => __('Small')
            ]
        ];
    }

    /**
     * Retrieve labels size type list
     *
     * @return array
     */
    public function getSizeList()
    {
        return [
            self::LARGE,
            self::MEDIUM,
            self::SMALL
        ];
    }
}
