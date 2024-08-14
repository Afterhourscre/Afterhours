<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model;

use Aheadworks\OnSale\Model\Config;
use Aheadworks\OnSale\Model\Source\Label\Position\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class ConfigTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $model;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->model = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    /**
     * Test getMarginBetweenLabels method
     */
    public function testGetMarginBetweenLabels()
    {
        $expected = 10;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_MARGIN_BETWEEN_LABELS)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getMarginBetweenLabels());
    }

    /**
     * Test getLabelsLineUpType method
     */
    public function testGetLabelsLineUpType()
    {
        $expected = 'vertical';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(Config::XML_PATH_GENERAL_LABELS_LINEUP_TYPE)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getLabelsLineUpType());
    }

    /**
     * Test getMaxNumberOfLabelsByArea method
     *
     * @param string $area
     * @param int $expected
     * @dataProvider areaDataProvider
     */
    public function testGetMaxNumberOfLabelsByArea($area, $expected)
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getMaxNumberOfLabelsByArea($area));
    }

    /**
     * Data provider for getMaxNumberOfLabelsByArea test
     *
     * @return array
     */
    public function areaDataProvider()
    {
        return [
            [Area::PRODUCT_IMAGE, 2],
            [Area::NEXT_TO_PRICE, 3]
        ];
    }
}
