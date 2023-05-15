<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor;

use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\StockProcessor;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\CatalogInventory\Api\StockStateInterface;

/**
 * Class StockProcessorTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor
 */
class StockProcessorTest extends TestCase
{
    /**
     * @var StockProcessor
     */
    private $model;

    /**
     * @var StockStateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stockStateMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->stockStateMock = $this->getMockForAbstractClass(StockStateInterface::class);
        $this->model = $objectManager->getObject(
            StockProcessor::class,
            [
                'stockState' => $this->stockStateMock
            ]
        );
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $expected = 5;
        $params = [];
        $productId = 1;

        $productMock = $this->createPartialMock(Product::class, ['getId']);
        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn($productId);

        $this->stockStateMock->expects($this->once())
            ->method('getStockQty')
            ->with($productId)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->process($productMock, $params));
    }

    /**
     * Test processTest method
     */
    public function testProcessTest()
    {
        $expected = '20';
        $params = [];

        $this->assertEquals($expected, $this->model->processTest($params));
    }
}
