<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor;

use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\SpdlProcessor;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class SpdlProcessorTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor
 */
class SpdlProcessorTest extends TestCase
{
    /**
     * @var SpdlProcessor
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
            SpdlProcessor::class,
            []
        );
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $expected = '2018-06-01 00:00:00';
        $params = [];

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getSpecialToDate')
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->process($productMock, $params));
    }

    /**
     * Test processTest method
     */
    public function testProcessTest()
    {
        $expectedDate = new \DateTime('today');
        $expectedDate->modify('+2 day');
        $result = $expectedDate->format(DateTime::DATETIME_INTERNAL_FORMAT);

        $params = [];
        $this->assertEquals($result, $this->model->processTest($params));
    }
}
