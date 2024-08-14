<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor;

use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\AttributeProcessor;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

/**
 * Class AttributeProcessorTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor
 */
class AttributeProcessorTest extends TestCase
{
    /**
     * @var AttributeProcessor
     */
    private $model;

    /**
     * @var ProductRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepositoryMock;

    /**
     * @var ProductResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productResourceMock;

    /**
     * @var string
     */
    private $productSku = 'sku';

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->productRepositoryMock = $this->getMockForAbstractClass(ProductRepositoryInterface::class);
        $this->productResourceMock = $this->createPartialMock(ProductResource::class, ['getAttribute']);
        $this->model = $objectManager->getObject(
            AttributeProcessor::class,
            [
                'productRepository' => $this->productRepositoryMock,
                'productResource' => $this->productResourceMock
            ]
        );
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $expected = 'attribute_value';
        $params = ['attribute_code'];

        $productMock = $this->initProduct();
        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->productSku)
            ->willReturn($productMock);
        $this->productResourceMock->expects($this->once())
            ->method('getAttribute')
            ->with($params[0])
            ->willReturn($expected);
        $productMock->expects($this->once())
            ->method('getAttributeText')
            ->with($params[0])
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->process($productMock, $params));
    }

    /**
     * Test process method on exception
     */
    public function testProcessOnException()
    {
        $expected = '';
        $params = ['attribute_code'];
        $exception = new \Exception('message');

        $productMock = $this->initProduct();
        $this->productRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->productSku)
            ->willThrowException($exception);

        $this->assertEquals($expected, $this->model->process($productMock, $params));
    }

    /**
     * Test processTest method
     */
    public function testProcessTest()
    {
        $expected = 'attr value';
        $params = [];

        $this->assertEquals($expected, $this->model->processTest($params));
    }

    /**
     * Init product mock
     *
     * @return ProductInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function initProduct()
    {
        $productMock = $this->createPartialMock(Product::class, ['getSku', 'getAttributeText']);
        $productMock->expects($this->once())
            ->method('getSku')
            ->willReturn($this->productSku);

        return $productMock;
    }
}
