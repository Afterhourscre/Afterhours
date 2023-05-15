<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor\ProductType;

use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\PriceProcessor;
use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price\PriceInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class PriceProcessorTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor\ProductType
 */
class PriceProcessorTest extends TestCase
{
    /**
     * @var PriceProcessor
     */
    private $model;

    /**
     * @var PriceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $processorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->processorMock = $this->getMockForAbstractClass(PriceInterface::class);
        $this->model = $objectManager->getObject(
            PriceProcessor::class,
            [
                'processors' => [
                    'default' => $this->processorMock,
                    'configurable' => $this->processorMock
                ]
            ]
        );
    }

    /**
     * Test prepareRegularPrice method
     *
     * @param string $productType
     * @param float $processedValue
     * @param float $expected
     * @dataProvider prepareRegularPriceDataProvider
     */
    public function testPrepareRegularPrice($productType, $processedValue, $expected)
    {
        $productMock = $this->getMockForAbstractClass(ProductInterface::class);
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn($productType);

        $this->processorMock->expects($this->any())
            ->method('getRegularPrice')
            ->willReturn($processedValue);

        $this->assertEquals($expected, $this->model->prepareRegularPrice($productMock));
    }

    /**
     * Data provider for testPrepareRegularPrice test
     *
     * @return array
     */
    public function prepareRegularPriceDataProvider()
    {
        return [
            ['bundle', 20, 0],
            ['configurable', 15.50, 15.50],
            ['virtual', 50, 50]
        ];
    }

    /**
     * Test prepareSpecialPrice method
     *
     * @param string $productType
     * @param float $processedValue
     * @param float $expected
     * @dataProvider prepareSpecialPriceDataProvider
     */
    public function testPrepareSpecialPrice($productType, $processedValue, $expected)
    {
        $productMock = $this->getMockForAbstractClass(ProductInterface::class);
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn($productType);

        $this->processorMock->expects($this->any())
            ->method('getSpecialPrice')
            ->willReturn($processedValue);

        $this->assertEquals($expected, $this->model->prepareSpecialPrice($productMock));
    }

    /**
     * Data provider for testPrepareSpecialPrice test
     *
     * @return array
     */
    public function prepareSpecialPriceDataProvider()
    {
        return [
            ['grouped', 50, 0],
            ['configurable', 16.50, 16.50],
            ['virtual', 50, 50]
        ];
    }
}
