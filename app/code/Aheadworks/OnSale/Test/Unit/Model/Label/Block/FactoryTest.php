<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block;

use Aheadworks\OnSale\Api\Data\BlockInterface;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Model\Label\Block\Factory;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;
use Aheadworks\OnSale\Api\Data\BlockInterfaceFactory;
use Aheadworks\OnSale\Model\Label\Block;

/**
 * Class FactoryTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block
 */
class FactoryTest extends TestCase
{
    /**
     * @var Factory
     */
    private $model;

    /**
     * @var VariableProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $labelTextVariableProcessorMock;

    /**
     * @var BlockInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blockFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->labelTextVariableProcessorMock = $this->createPartialMock(
            VariableProcessor::class,
            ['processVariableInLabelText', 'processVariableInLabelTestText']
        );
        $this->blockFactoryMock = $this->createPartialMock(BlockInterfaceFactory::class, ['create']);
        $this->model = $objectManager->getObject(
            Factory::class,
            [
                'labelTextVariableProcessor' => $this->labelTextVariableProcessorMock,
                'blockFactory' => $this->blockFactoryMock
            ]
        );
    }

    /**
     * Testing of create method
     */
    public function testCreate()
    {
        $labelTexts = ['text_large', 'text_medium', 'text_small'];
        $productMock = $this->createMock(Product::class);
        $labelMock = $this->getMockForAbstractClass(LabelInterface::class);
        $blockMock = $this->createPartialMock(
            Block::class,
            ['setData', 'setLabel','setLabelText', 'setLabelTextVariableValues']
        );
        $variableValues = [];

        $this->createBlock($labelMock, $labelTexts, $blockMock, $variableValues, false);

        $this->assertEquals($blockMock, $this->model->create($labelMock, $labelTexts, $productMock));
    }

    /**
     * Testing of createForTest method
     */
    public function testCreateForTest()
    {
        $labelText = 'text';
        $labelMock = $this->getMockForAbstractClass(LabelInterface::class);
        $blockMock = $this->createPartialMock(
            Block::class,
            ['setData', 'setLabel','setLabelText', 'setLabelTextVariableValues', 'setLabelSize']
        );
        $blockMock->expects($this->once())
            ->method('setLabelSize')
            ->willReturn('large');
        $variableValues = [];

        $this->createBlock($labelMock, $labelText, $blockMock, $variableValues, true);

        $this->assertEquals($blockMock, $this->model->createForTest($labelMock, $labelText));
    }

    /**
     * Create block initialize
     *
     * @param LabelInterface|\PHPUnit_Framework_MockObject_MockObject $labelMock
     * @param string|array $labelTextData
     * @param Product|\PHPUnit_Framework_MockObject_MockObject $productMock
     * @param BlockInterface|\PHPUnit_Framework_MockObject_MockObject $blockMock
     * @param array $variableValues
     * @param bool $forTest
     */
    private function createBlock($labelMock, $labelTextData, $blockMock, $variableValues, $forTest)
    {
        $this->blockFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($blockMock);
        $blockMock->expects($this->once())
            ->method('setLabel')
            ->with($labelMock)
            ->willReturnSelf();

        $blockMock->expects($this->exactly(3))
            ->method('setData')
            ->withAnyParameters()
            ->willReturnSelf();
        if ($forTest) {
            $this->labelTextVariableProcessorMock->expects($this->exactly(3))
                ->method('processVariableInLabelTestText')
                ->withAnyParameters()
                ->willReturn($variableValues);
        } else {
            $this->labelTextVariableProcessorMock->expects($this->exactly(3))
                ->method('processVariableInLabelText')
                ->withAnyParameters()
                ->willReturn($variableValues);
        }

        $blockMock->expects($this->once())
            ->method('setLabelTextVariableValues')
            ->with($variableValues)
            ->willReturnSelf();
    }
}
