<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Test\Unit\Controller\Adminhtml\Earning\Rules\Condition;

use Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\Condition\Factory as RuleConditionFactory;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\CatalogRule\Model\Rule\Condition\Product as ProductCondition;
use Magento\CatalogRule\Model\Rule;

/**
 * Test for \Aheadworks\RewardPoints\Controller\Adminhtml\Earning\Rules\Condition\Factory
 */
class FactoryTest extends TestCase
{
    /**
     * @var RuleConditionFactory
     */
    private $factory;

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);

        $this->factory = $objectManager->getObject(
            RuleConditionFactory::class,
            [
                'objectManager' => $this->objectManagerMock,
            ]
        );
    }

    /**
     * Test process method
     *
     * @param string|null $attribute
     * @dataProvider processDataProvider
     * @throws \Exception
     */
    public function testProcess($attribute)
    {
        $type = ProductCondition::class;
        $id = '1--1';
        $prefix = 'conditions';
        $jsFormObject = 'rule_conditions_fieldset';
        $formName = 'aw_reward_points_earning_rules_form';

        $conditionMock = $this->createPartialMock(
            $type,
            ['setId', 'setType', 'setRule', 'setPrefix', 'setAttribute', 'setJsFormObject', 'setFormName']
        );
        $ruleMock = $this->createMock(Rule::class);

        $this->setupMocks($conditionMock, $id, $type, $ruleMock, $prefix, $attribute, $jsFormObject, $formName);

        $this->objectManagerMock->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive([ProductCondition::class], [Rule::class])
            ->willReturnOnConsecutiveCalls($conditionMock, $ruleMock);

        $this->assertEquals(
            $conditionMock,
            $this->factory->create($type, $id, $prefix, $attribute, $jsFormObject, $formName)
        );
    }

    /**
     * Set up mocks
     *
     * @param ConditionCombine|\PHPUnit_Framework_MockObject_MockObject $conditionMock
     * @param string $id
     * @param string $type
     * @param Rule|\PHPUnit_Framework_MockObject_MockObject $ruleMock
     * @param string $prefix
     * @param string|null $attribute
     * @param string $jsFormObject
     * @param string $formName
     */
    private function setupMocks($conditionMock, $id, $type, $ruleMock, $prefix, $attribute, $jsFormObject, $formName)
    {
        $conditionMock->expects($this->once())
            ->method('setId')
            ->with($id)
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setType')
            ->with($type)
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setRule')
            ->with($ruleMock)
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setPrefix')
            ->with($prefix)
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setJsFormObject')
            ->with($jsFormObject)
            ->willReturnSelf();
        $conditionMock->expects($this->once())
            ->method('setFormName')
            ->with($formName)
            ->willReturnSelf();

        if ($attribute) {
            $conditionMock->expects($this->once())
                ->method('setAttribute')
                ->with($attribute)
                ->willReturnSelf();
        }
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            ['attribute' => 'value'],
            ['attribute' => null]
        ];
    }

    /**
     * Test process method if incorrect condition specified
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Condition must be instance of AbstractCondition
     */
    public function testProcessIncorrectCondition()
    {
        $type = DataObject::class;
        $id = '1--1';
        $prefix = 'conditions';
        $attribute = null;
        $jsFormObject = 'rule_conditions_fieldset';
        $formName = 'aw_reward_points_earning_rules_form';

        $dataObjectMock = $this->createMock($type);

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(DataObject::class)
            ->willReturn($dataObjectMock);

        $this->factory->create($type, $id, $prefix, $attribute, $jsFormObject, $formName);
    }
}
