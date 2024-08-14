<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label;

use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;
use Magento\Catalog\Api\Data\ProductInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class VariableProcessorTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label
 */
class VariableProcessorTest extends TestCase
{
    /**
     * @var VariableProcessor
     */
    private $model;

    /**
     * @var ProductInterface|\PHPUnit_Framework_MockObject_MockObject
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

        $this->processorMock = $this->getMockForAbstractClass(VariableProcessor\VariableProcessorInterface::class);
        $this->model = $objectManager->getObject(
            VariableProcessor::class,
            [
                'processors' => [
                    'br' => $this->processorMock,
                    'attribute' => $this->processorMock
                ]
            ]
        );
    }

    /**
     * Test processVariableInLabelText method
     *
     * @param string $labelText
     * @param mixed $processedValue
     * @param array $expected
     * @dataProvider processVariableInLabelTextDataProvider
     */
    public function testProcessVariableInLabelText($labelText, $processedValue, $expected)
    {
        $productMock = $this->getMockForAbstractClass(ProductInterface::class);

        $this->processorMock->expects($this->atLeastOnce())
            ->method('process')
            ->willReturn($processedValue);

        $this->assertEquals($expected, $this->model->processVariableInLabelText($productMock, $labelText));
    }

    /**
     * Data provider for processVariableInLabelText test
     *
     * @return array
     */
    public function processVariableInLabelTextDataProvider()
    {
        return [
            ['{br}', '<br>', ['br' => '<br>']],
            ['{attribute:code}', ['val1', 'val2'], ['attribute:code' => 'val1 val2']],
            ['{attribute:code}', 'attribute value', ['attribute:code' => 'attribute value']]
        ];
    }

    /**
     * Test processVariableInLabelTestText method
     *
     * @param string $labelText
     * @param mixed $processedValue
     * @param array $expected
     * @dataProvider processVariableInLabelTestTextDataProvider
     */
    public function testProcessVariableInLabelTestText($labelText, $processedValue, $expected)
    {
        $this->processorMock->expects($this->atLeastOnce())
            ->method('processTest')
            ->willReturn($processedValue);

        $this->assertEquals(
            $expected,
            $this->model->processVariableInLabelTestText($labelText)
        );
    }

    /**
     * Data provider for processVariableInLabelTestText test
     *
     * @return array
     */
    public function processVariableInLabelTestTextDataProvider()
    {
        return [
            ['{br}', '<br>', ['br' => '<br>']],
            ['{attribute:code}', ['val1', 'val2'], ['attribute:code' => 'val1 val2']],
            ['{attribute:code}', 'attribute value', ['attribute:code' => 'attribute value']]
        ];
    }
}
