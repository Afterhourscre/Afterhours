<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor;

use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\SpecialPriceProcessor;
use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\PriceProcessor
    as ProductTypePriceProcessor;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

/**
 * Class SpecialPriceProcessorTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor
 */
class SpecialPriceProcessorTest extends TestCase
{
    /**
     * @var SpecialPriceProcessor
     */
    private $model;

    /**
     * @var PriceHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceHelperMock;

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
        $this->priceHelperMock = $this->createPartialMock(PriceHelper::class, ['currencyByStore']);
        $this->productTypePriceProcessor = $this->createPartialMock(
            ProductTypePriceProcessor::class,
            ['prepareSpecialPrice']
        );
        $this->model = $objectManager->getObject(
            SpecialPriceProcessor::class,
            [
                'priceHelper' => $this->priceHelperMock,
                'productTypePriceProcessor' => $this->productTypePriceProcessor
            ]
        );
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $expected = '$5';
        $params = [];
        $product = [
            'special_price' => '5',
            'store_id' => '1'
        ];

        $productMock = $this->createPartialMock(Product::class, ['getStoreId']);
        $this->productTypePriceProcessor->expects($this->once())
            ->method('prepareSpecialPrice')
            ->with($productMock)
            ->willReturn($product['special_price']);
        $productMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($product['store_id']);

        $this->priceHelperMock->expects($this->once())
            ->method('currencyByStore')
            ->with($product['special_price'], $product['store_id'], true, false)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->process($productMock, $params));
    }

    /**
     * Test processTest method
     */
    public function testProcessTest()
    {
        $expected = '$20';
        $params = [];

        $this->assertEquals($expected, $this->model->processTest($params));
    }
}
