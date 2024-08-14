<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model\Converter;

use Aheadworks\Coupongenerator\Model\Converter\Condition;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\SalesRule\Api\Data\ConditionInterfaceFactory;
use Magento\SalesRule\Api\Data\ConditionInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Model\Converter\Condition
 */
class ConditionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Condition
     */
    private $model;

    /**
     * @var ConditionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->conditionFactoryMock = $this->getMockBuilder(ConditionInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->model = $objectManager->getObject(
            Condition::class,
            [
                'conditionFactory' => $this->conditionFactoryMock
            ]
        );
    }

    /**
     * Test arrayToDataModel method
     *
     * @param array $conditions
     * @dataProvider getConditionDataProvider
     */
    public function testArrayToDataModel($conditions)
    {
        $conditionMock = $this->getMockBuilder(ConditionInterface::class)
            ->getMock();
        $conditionChildMock = $this->getMockBuilder(ConditionInterface::class)
            ->getMock();
        $childCondition = $conditions['conditions'][0];

        $this->conditionFactoryMock
            ->expects($this->at(0))
            ->method('create')
            ->willReturn($conditionMock);
        $this->conditionFactoryMock
            ->expects($this->at(1))
            ->method('create')
            ->willReturn($conditionChildMock);

        $conditionMock->expects($this->once())
            ->method('setConditionType')
            ->with($conditions['type'])
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setAggregatorType')
            ->with($conditions['aggregator'])
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setAttributeName')
            ->with($conditions['attribute'])
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setOperator')
            ->with($conditions['operator'])
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setValue')
            ->with($conditions['value'])
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setConditions')
            ->with([$conditionChildMock])
            ->willReturnSelf();

        $conditionChildMock->expects($this->once())
            ->method('setConditionType')
            ->with($childCondition['type'])
            ->willReturnSelf();
        $conditionChildMock->expects($this->once())
            ->method('setAttributeName')
            ->with($childCondition['attribute'])
            ->willReturnSelf();
        $conditionChildMock->expects($this->once())
            ->method('setAggregatorType')
            ->with($childCondition['aggregator'])
            ->willReturnSelf();
        $conditionChildMock->expects($this->once())
            ->method('setOperator')
            ->with($childCondition['operator'])
            ->willReturnSelf();
        $conditionChildMock->expects($this->once())
            ->method('setValue')
            ->with($childCondition['value'])
            ->willReturnSelf();

        $this->assertEquals($conditionMock, $this->model->arrayToDataModel($conditions));
    }

    /**
     * Test dataModelToArray method
     *
     * @param array $conditions
     * @dataProvider getConditionDataProvider
     */
    public function testDataModelToArray($conditions)
    {
        $dataModelMock = $this->getMockBuilder(ConditionInterface::class)
            ->getMock();
        $childConditionMock = $this->getMockBuilder(ConditionInterface::class)
            ->getMock();
        $childCondition = $conditions['conditions'][0];

        $dataModelMock->expects($this->once())
            ->method('getConditionType')
            ->willReturn($conditions['type']);
        $dataModelMock->expects($this->once())
            ->method('getAttributeName')
            ->willReturn($conditions['attribute']);
        $dataModelMock->expects($this->once())
            ->method('getOperator')
            ->willReturn($conditions['operator']);
        $dataModelMock->expects($this->once())
            ->method('getValue')
            ->willReturn($conditions['value']);
        $dataModelMock->expects($this->once())
            ->method('getAggregatorType')
            ->willReturn($conditions['aggregator']);
        $dataModelMock->expects($this->once())
            ->method('getConditions')
            ->willReturn([$childConditionMock]);

        $childConditionMock->expects($this->once())
            ->method('getConditionType')
            ->willReturn($childCondition['type']);
        $childConditionMock->expects($this->once())
            ->method('getAttributeName')
            ->willReturn($childCondition['attribute']);
        $childConditionMock->expects($this->once())
            ->method('getOperator')
            ->willReturn($childCondition['operator']);
        $childConditionMock->expects($this->once())
            ->method('getValue')
            ->willReturn($childCondition['value']);
        $childConditionMock->expects($this->once())
            ->method('getAggregatorType')
            ->willReturn($childCondition['aggregator']);
        $childConditionMock->expects($this->once())
            ->method('getConditions')
            ->willReturn([]);

        $this->assertEquals($conditions, $this->model->dataModelToArray($dataModelMock));
    }

    /**
     * Data provider for tests
     *
     * @return array
     */
    public function getConditionDataProvider()
    {
        return [
            [
                [
                    'type' => 'type',
                    'attribute' => 'attribute',
                    'operator' => 'operator',
                    'value' => 'value',
                    'aggregator' => 'aggregator',
                    'conditions' => [
                        [
                            'type' => 'child_type',
                            'attribute' => 'child_attribute',
                            'operator' => 'child_operator',
                            'value' => 'child_value',
                            'aggregator' => 'aggregator'
                        ]
                    ]
                ]
            ]
        ];
    }
}
