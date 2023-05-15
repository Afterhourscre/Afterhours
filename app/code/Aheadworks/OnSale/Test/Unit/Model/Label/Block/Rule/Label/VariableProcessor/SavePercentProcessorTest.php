<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor;

use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\SavePercentProcessor;
use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\PriceProcessor
    as ProductTypePriceProcessor;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class SavePercentProcessorTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor
 */
class SavePercentProcessorTest extends TestCase
{
    /**
     * @var SavePercentProcessor
     */
    private $model;

    /**
     * @var ProductTypePriceProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productTypePriceProcessor;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->productTypePriceProcessor = $this->createPartialMock(
            ProductTypePriceProcessor::class,
            ['prepareSpecialPrice', 'prepareRegularPrice']
        );

        $this->model = $objectManager->getObject(
            SavePercentProcessor::class,
            [
                'productTypePriceProcessor' => $this->productTypePriceProcessor
            ]
        );
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $expected = '50%';
        $params = [];
        $product = [
            'price' => '10',
            'special_price' => '5'
        ];

        $productMock = $this->createPartialMock(Product::class, []);
        $this->productTypePriceProcessor->expects($this->once())
            ->method('prepareRegularPrice')
            ->with($productMock)
            ->willReturn($product['price']);
        $this->productTypePriceProcessor->expects($this->once())
            ->method('prepareSpecialPrice')
            ->with($productMock)
            ->willReturn($product['special_price']);

        $this->assertEquals($expected, $this->model->process($productMock, $params));
    }

    /**
     * Test processTest method
     */
    public function testProcessTest()
    {
        $expected = '20%';
        $params = [];

        $this->assertEquals($expected, $this->model->processTest($params));
    }
}
