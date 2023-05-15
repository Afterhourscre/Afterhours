<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Test\Unit\Model\ResourceModel\EarnRule\Hydrator;

use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Hydrator\Condition as ConditionHydrator;
use Aheadworks\RewardPoints\Api\Data\ConditionInterface;
use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\Condition;
use Aheadworks\RewardPoints\Model\EarnRule\Condition\Converter as ConditionConverter;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Hydrator\Condition
 */
class ConditionTest extends TestCase
{
    /**
     * @var ConditionHydrator
     */
    private $hydrator;

    /**
     * @var ConditionConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionConverterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->conditionConverterMock = $this->createMock(ConditionConverter::class);

        $this->hydrator = $objectManager->getObject(
            ConditionHydrator::class,
            [
                'conditionConverter' => $this->conditionConverterMock
            ]
        );
    }

    /**
     * Test extract method
     */
    public function testExtract()
    {
        $conditionMock = $this->createMock(ConditionInterface::class);
        $conditionData = [
            Condition::AGGREGATOR => 'all'
        ];
        $serializedConditionData = serialize($conditionData);
        $result = [
            EarnRuleInterface::CONDITION => $serializedConditionData
        ];

        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getCondition')
            ->willReturn($conditionMock);

        $this->conditionConverterMock->expects($this->once())
            ->method('dataModelToArray')
            ->with($conditionMock)
            ->willReturn($conditionData);

        $this->assertEquals($result, $this->hydrator->extract($ruleMock));
    }

    /**
     * Test extract method if no condition
     */
    public function testExtractNoCondition()
    {
        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getCondition')
            ->willReturn(null);

        $this->assertEquals([], $this->hydrator->extract($ruleMock));
    }

    /**
     * Test hydrate method
     */
    public function testHydrate()
    {
        $conditionData = [
            Condition::AGGREGATOR => 'all'
        ];
        $serializedConditionData = serialize($conditionData);
        $data = [
            EarnRuleInterface::CONDITION => $serializedConditionData
        ];
        $conditionMock = $this->createMock(ConditionInterface::class);

        $this->conditionConverterMock->expects($this->once())
            ->method('arrayToDataModel')
            ->with($conditionData)
            ->willReturn($conditionMock);

        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('setCondition')
            ->with($conditionMock)
            ->willReturnSelf();

        $this->assertEquals($ruleMock, $this->hydrator->hydrate($ruleMock, $data));
    }

    /**
     * Test hydrate method if no condition data
     */
    public function testHydrateNoCondition()
    {
        $data = [];

        $ruleMock = $this->createMock(EarnRuleInterface::class);
        $ruleMock->expects($this->never())
            ->method('setCondition');

        $this->assertEquals($ruleMock, $this->hydrator->hydrate($ruleMock, $data));
    }
}
