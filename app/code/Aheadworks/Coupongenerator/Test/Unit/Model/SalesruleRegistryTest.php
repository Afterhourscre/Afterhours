<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model;

use Aheadworks\Coupongenerator\Model\SalesruleRegistry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Model\SalesruleRegistry
 */
class SalesruleRegistryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SalesruleRegistry
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
            SalesruleRegistry::class,
            []
        );
    }

    /**
     * Test retrieve method on null
     */
    public function testRetrieveNull()
    {
        $salesruleId = 1;
        $this->assertNull($this->model->retrieve($salesruleId));
    }

    /**
     * Test retrieveByRuleId method on null
     */
    public function testRetrieveByRuleIdNull()
    {
        $ruleId = 1;
        $this->assertNull($this->model->retrieveByRuleId($ruleId));
    }

    /**
     * Test retrieve method on object
     */
    public function testRetrieveObject()
    {
        $salesruleId = 1;
        $ruleId = 2;
        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);

        $salesruleMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($salesruleId);
        $salesruleMock->expects($this->once())
            ->method('getRuleId')
            ->willReturn($ruleId);

        $this->model->push($salesruleMock);

        $this->assertEquals($salesruleMock, $this->model->retrieve($salesruleId));
    }

    /**
     * Test retrieve method on object
     */
    public function testRetrieveByRuleIdObject()
    {
        $salesruleId = 1;
        $ruleId = 2;
        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);

        $salesruleMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($salesruleId);
        $salesruleMock->expects($this->once())
            ->method('getRuleId')
            ->willReturn($ruleId);

        $this->model->push($salesruleMock);

        $this->assertEquals($salesruleMock, $this->model->retrieveByRuleId($ruleId));
    }

    /**
     * Test remove method
     */
    public function testRemove()
    {
        $salesruleId = 1;
        $ruleId = 2;
        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);

        $salesruleMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($salesruleId);
        $salesruleMock->expects($this->exactly(2))
            ->method('getRuleId')
            ->willReturn($ruleId);

        $this->model->push($salesruleMock);

        $salesruleFromReg = $this->model->retrieve($salesruleId);
        $this->assertEquals($salesruleMock, $salesruleFromReg);

        $this->model->remove($salesruleId);
        $this->assertNull($this->model->retrieve($salesruleId));
    }

    /**
     * Test removeByRuleId method
     */
    public function testRemoveByRuleId()
    {
        $salesruleId = 1;
        $ruleId = 2;
        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);

        $salesruleMock->expects($this->exactly(3))
            ->method('getId')
            ->willReturn($salesruleId);
        $salesruleMock->expects($this->exactly(1))
            ->method('getRuleId')
            ->willReturn($ruleId);

        $this->model->push($salesruleMock);

        $salesruleFromReg = $this->model->retrieveByRuleId($ruleId);
        $this->assertEquals($salesruleMock, $salesruleFromReg);

        $this->model->removeByRuleId($ruleId);
        $this->assertNull($this->model->retrieveByRuleId($ruleId));
    }
}
