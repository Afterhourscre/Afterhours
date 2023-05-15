<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Source\Label\Position;

use Aheadworks\OnSale\Model\Source\Label\Position;
use Aheadworks\OnSale\Model\Source\Label\Position\Area;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class AreaTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Source\Label\Position
 */
class AreaTest extends TestCase
{
    /**
     * @var Area
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            Area::class,
            []
        );
    }

    /**
     * Test getAreaValues method
     */
    public function testGetAreaValues()
    {
        $this->assertTrue(is_array($this->model->getAreaValues()));
    }

    /**
     * Test getPositionByArea method
     *
     * @param string $area
     * @param string $expected
     * @dataProvider getPositionByAreaDataProvider
     */
    public function testGetPositionByArea($area, $expected)
    {
        $this->assertEquals($expected, $this->model->getPositionByArea($area));
    }

    /**
     * Data provider for getPositionByArea test
     *
     * @return array
     */
    public function getPositionByAreaDataProvider()
    {
        return [
            [
                Area::PRODUCT_IMAGE,
                [
                    Position::TOP_LEFT,
                    Position::TOP_RIGHT,
                    Position::BOTTOM_LEFT,
                    Position::BOTTOM_RIGHT
                ]
            ],
            [Area::NEXT_TO_PRICE, [Position::NEXT_TO_PRICE]]
        ];
    }

    /**
     * Test getAreaByPosition method
     *
     * @param string $position
     * @param string $expected
     * @dataProvider getAreaByPositionDataProvider
     */
    public function testGetAreaByPosition($position, $expected)
    {
        $this->assertEquals($expected, $this->model->getAreaByPosition($position));
    }

    /**
     * Data provider for getPositionByArea test
     *
     * @return array
     */
    public function getAreaByPositionDataProvider()
    {
        return [
            [Position::TOP_LEFT, Area::PRODUCT_IMAGE],
            [Position::TOP_RIGHT, Area::PRODUCT_IMAGE],
            [Position::BOTTOM_LEFT, Area::PRODUCT_IMAGE],
            [Position::BOTTOM_RIGHT, Area::PRODUCT_IMAGE],
            [Position::NEXT_TO_PRICE, Area::NEXT_TO_PRICE],
            ['undefined', null]
        ];
    }

    /**
     * Test getPositionByAreaMap method
     */
    public function testGetPositionByAreaMap()
    {
        $this->assertTrue(is_array($this->model->getPositionByAreaMap()));
    }
}
