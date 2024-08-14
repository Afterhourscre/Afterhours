<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Source\Label;

use Aheadworks\OnSale\Model\Source\Label\Position;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class PositionTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Source\Label
 */
class PositionTest extends TestCase
{
    /**
     * @var Position
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
            Position::class,
            []
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->model->toOptionArray()));
    }

    /**
     * Test getPositionClassesMap method
     */
    public function testGetPositionClassesMap()
    {
        $this->assertTrue(is_array($this->model->getPositionClassesMap()));
    }

    /**
     * Test getInvertLabelPositions method
     */
    public function testGetInvertLabelPositions()
    {
        $this->assertTrue(is_array($this->model->getInvertLabelPositions()));
    }

    /**
     * Test getClassByPosition method
     *
     * @param string $position
     * @param string $expected
     * @dataProvider getClassByPositionDataProvider
     */
    public function testGetClassByPosition($position, $expected)
    {
        $this->assertEquals($expected, $this->model->getClassByPosition($position));
    }

    /**
     * Data provider for getInvertLabelPositions test
     *
     * @return array
     */
    public function getClassByPositionDataProvider()
    {
        return [
            [Position::TOP_LEFT, 'top-left'],
            [Position::TOP_RIGHT, 'top-right'],
            [Position::BOTTOM_LEFT, 'bottom-left'],
            [Position::BOTTOM_RIGHT, 'bottom-right'],
            [Position::NEXT_TO_PRICE, 'next-to-price']
        ];
    }

    /**
     * Test isInvertLabelByPosition method
     *
     * @param string $position
     * @param string $expected
     * @dataProvider isInvertLabelByPositionDataProvider
     */
    public function testIsInvertLabelByPosition($position, $expected)
    {
        $this->assertEquals($expected, $this->model->isInvertLabelByPosition($position));
    }

    /**
     * Data provider for isInvertLabelByPosition test
     *
     * @return array
     */
    public function isInvertLabelByPositionDataProvider()
    {
        return [
            [Position::TOP_LEFT, false],
            [Position::TOP_RIGHT, false],
            [Position::BOTTOM_LEFT, true],
            [Position::BOTTOM_RIGHT, true],
            [Position::NEXT_TO_PRICE, false]
        ];
    }
}
