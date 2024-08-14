<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\CouponVariableManagement;
use Aheadworks\Acr\Api\Data\CouponVariableInterface;
use Aheadworks\Acr\Api\Data\CouponVariableInterfaceFactory;
use Aheadworks\Acr\Model\Rule\Coupon\Processor as CouponProcessor;
use Aheadworks\Acr\Model\Rule\Coupon\Generator as CouponGenerator;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Api\Data\CouponRuleInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;

/**
 * Class CouponVariableManagementTest
 * Test for \Aheadworks\Acr\Model\CouponVariableManagement
 *
 * @package Aheadworks\Acr\Test\Unit\Model
 */
class CouponVariableManagementTest extends TestCase
{
    /**
     * @var CouponVariableManagement
     */
    private $model;

    /**
     * @var CouponGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponGeneratorMock;

    /**
     * @var CouponProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponProcessorMock;

    /**
     * @var CouponVariableInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponVariableFactoryMock;

    /**
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRepositoryMock;

    /**
     * @var CouponRepositoryInterface
     */
    private $couponRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->couponGeneratorMock = $this->createPartialMock(CouponGenerator::class, ['getCoupon']);
        $this->couponProcessorMock = $this->createPartialMock(CouponProcessor::class, ['getCouponVariable']);
        $this->couponVariableFactoryMock = $this->createPartialMock(CouponVariableInterfaceFactory::class, ['create']);
        $this->ruleRepositoryMock = $this->getMockForAbstractClass(RuleRepositoryInterface::class);
        $this->couponRepositoryMock = $this->getMockForAbstractClass(CouponRepositoryInterface::class);
        $this->model = $objectManager->getObject(
            CouponVariableManagement::class,
            [
                'couponGenerator' => $this->couponGeneratorMock,
                'couponProcessor' => $this->couponProcessorMock,
                'couponVariableFactory' => $this->couponVariableFactoryMock,
                'ruleRepository' => $this->ruleRepositoryMock,
                'couponRepository' => $this->couponRepositoryMock
            ]
        );
    }

    /**
     * Test getCouponVariable method
     */
    public function testGetCouponVariable()
    {
        $ruleId = 1;
        $storeId = 1;
        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $this->couponVariableFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($ruleId)
            ->willReturn($ruleMock);

        $couponRuleMock = $this->getMockForAbstractClass(CouponRuleInterface::class);
        $couponRuleMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn(true);
        $ruleMock->expects($this->once())
            ->method('getCouponRule')
            ->willReturn($couponRuleMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $this->couponGeneratorMock->expects($this->once())
            ->method('getCoupon')
            ->with($couponRuleMock)
            ->willReturn($couponMock);
        $couponVariableMock2 = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $this->couponProcessorMock->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock, $storeId)
            ->willReturn($couponVariableMock2);

        $this->assertEquals($couponVariableMock2, $this->model->getCouponVariable($ruleId, $storeId));
    }

    /**
     * Test getTestCouponVariable method
     */
    public function testGetTestCouponVariable()
    {
        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $this->couponVariableFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $couponVariableMock->expects($this->once())
            ->method('setCode')
            ->with('TEST')
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setDiscount')
            ->with('50%')
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setExpirationDate')
            ->with('Jan 20, 2020')
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setUsesPerCoupon')
            ->with(1)
            ->willReturnSelf();

        $this->assertEquals($couponVariableMock, $this->model->getTestCouponVariable());
    }
}
