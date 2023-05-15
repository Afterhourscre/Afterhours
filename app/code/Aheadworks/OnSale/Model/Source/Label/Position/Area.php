<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Source\Label\Position;

use Aheadworks\OnSale\Model\Source\Label\Position;

/**
 * Class Area
 *
 * @package Aheadworks\OnSale\Model\Source\Label\Position
 */
class Area
{
    /**#@+
     * Area values
     */
    const PRODUCT_IMAGE = 'product_image';
    const NEXT_TO_PRICE = 'next_to_price';
    /**#@-*/

    /**
     * Retrieve area values
     *
     * @return array
     */
    public function getAreaValues()
    {
        return [self::PRODUCT_IMAGE, self::NEXT_TO_PRICE];
    }

    /**
     * Retrieve position by area map
     *
     * @param string $area
     * @return array
     */
    public function getPositionByArea($area)
    {
        return $this->getPositionByAreaMap()[$area];
    }

    /**
     * Retrieve area by position
     *
     * @param string $position
     * @return string|null
     */
    public function getAreaByPosition($position)
    {
        $areaName = null;
        foreach ($this->getPositionByAreaMap() as $area => $positions) {
            if (array_search($position, $positions) !== false) {
                $areaName = $area;
            }
        }
        return $areaName;
    }

    /**
     * Retrieve position by area map
     *
     * @return array
     */
    public function getPositionByAreaMap()
    {
        return [
            self::PRODUCT_IMAGE => [
                Position::TOP_LEFT,
                Position::TOP_RIGHT,
                Position::BOTTOM_LEFT,
                Position::BOTTOM_RIGHT
            ],
            self::NEXT_TO_PRICE => [Position::NEXT_TO_PRICE]
        ];
    }
}
