<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor;

use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\SkuProcessor;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class SkuProcessorTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor
 */
class SkuProcessorTest extends TestCase
{
    /**
     * @var SkuProcessor
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
            SkuProcessor::class,
            []
        );
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $expected = 'sku';
        $params = [];

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getSku')
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->process($productMock, $params));
    }

    /**
     * Test processTest method
     */
    public function testProcessTest()
    {
        $expected = 'sku';
        $params = [];

        $this->assertEquals($expected, $this->model->processTest($params));
    }
}
