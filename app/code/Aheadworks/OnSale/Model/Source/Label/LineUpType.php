<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Source\Label;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class LineUpType
 *
 * @package Aheadworks\OnSale\Model\Source\Label
 */
class LineUpType implements OptionSourceInterface
{
    /**#@+
     * line up type values
     */
    const VERTICAL = 'vertical';
    const HORIZONTAL = 'horizontal';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::VERTICAL,
                'label' => __('Vertical')
            ],
            [
                'value' => self::HORIZONTAL,
                'label' => __('Horizontal')
            ]
        ];
    }

    /**
     * Retrieve line up by classes map
     *
     * @return array
     */
    public function getLineUpClassesMap()
    {
        return [
            self::VERTICAL => 'label-block',
            self::HORIZONTAL => 'label-inline-block'
        ];
    }

    /**
     * Retrieve class by line up type
     *
     * @param string $lineUpType
     * @return string
     */
    public function getClassByLineUpType($lineUpType)
    {
        return $this->getLineUpClassesMap()[$lineUpType];
    }
}
