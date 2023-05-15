<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Rule\ObjectDataProcessors;

use Aheadworks\OnSale\Model\Rule\ObjectDataProcessors\ProductCondition;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Model\Converter\Condition as ConditionConverter;
use Aheadworks\OnSale\Api\Data\ConditionInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class ProductConditionTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Rule\ObjectDataProcessors
 */
class ProductConditionTest extends TestCase
{
    /**
     * @var ProductCondition
     */
    private $model;

    /**
     * @var ConditionConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionConverterMock;

    /**
     * @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializerMock;

    /**
     * @var array
     */
    private $testConditionArray = [
        'value' => 10,
        'aggregator' => 'all'
    ];

    /**
     * @var string
     */
    private $testConditionSerializedArray = 'a:2:{s:5:"value";i:10;s:10:"aggregator";s:3:"all";}';

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->conditionConverterMock = $this->createPartialMock(
            ConditionConverter::class,
            ['arrayToDataModel', 'dataModelToArray']
        );
        $this->serializerMock = $this->createMock(SerializerInterface::class);

        $this->model = $objectManager->getObject(
            ProductCondition::class,
            [
                'conditionConverter' => $this->conditionConverterMock,
                'serializer' => $this->serializerMock,
            ]
        );
    }

    /**
     * Test for beforeSave method
     */
    public function testBeforeSave()
    {
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $ruleMock->expects($this->exactly(2))
            ->method('getProductCondition')
            ->willReturn($conditionMock);
        $this->conditionConverterMock->expects($this->once())
            ->method('dataModelToArray')
            ->with($conditionMock)
            ->willReturn($this->testConditionArray);

        $this->serializerMock->expects($this->once())
            ->method('serialize')
            ->with($this->testConditionArray)
            ->willReturn($this->testConditionSerializedArray);

        $ruleMock->expects($this->once())
            ->method('setProductCondition')
            ->with($this->testConditionSerializedArray)
            ->willReturnSelf();

        $this->assertSame($ruleMock, $this->model->beforeSave($ruleMock));
    }

    /**
     * Test for afterLoad method
     */
    public function testAfterLoad()
    {
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $conditionMock = $this->getMockForAbstractClass(ConditionInterface::class);
        $ruleMock->expects($this->exactly(2))
            ->method('getProductCondition')
            ->willReturn($this->testConditionSerializedArray);

        $this->serializerMock->expects($this->once())
            ->method('unserialize')
            ->with($this->testConditionSerializedArray)
            ->willReturn($this->testConditionArray);

        $this->conditionConverterMock->expects($this->once())
            ->method('arrayToDataModel')
            ->with($this->testConditionArray)
            ->willReturn($conditionMock);

        $ruleMock->expects($this->once())
            ->method('setProductCondition')
            ->with($conditionMock)
            ->willReturnSelf();

        $this->assertSame($ruleMock, $this->model->afterLoad($ruleMock));
    }
}
