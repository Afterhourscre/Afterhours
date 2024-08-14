<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Source\Label;

use Aheadworks\OnSale\Model\Source\Label\LineUpType;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class LineUpTypeTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Source\Label
 */
class LineUpTypeTest extends TestCase
{
    /**
     * @var LineUpType
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
            LineUpType::class,
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
     * Test getLineUpClassesMap method
     */
    public function testGetLineUpClassesMap()
    {
        $this->assertTrue(is_array($this->model->getLineUpClassesMap()));
    }

    /**
     * Test getClassByLineUpType method
     *
     * @param string $lineUpType
     * @param string $expected
     * @dataProvider areaDataProvider
     */
    public function testGetClassByLineUpType($lineUpType, $expected)
    {
        $this->assertEquals($expected, $this->model->getClassByLineUpType($lineUpType));
    }

    /**
     * Data provider for getMaxNumberOfLabelsByArea test
     *
     * @return array
     */
    public function areaDataProvider()
    {
        return [
            [LineUpType::VERTICAL, 'label-block'],
            [LineUpType::HORIZONTAL, 'label-inline-block']
        ];
    }
}
