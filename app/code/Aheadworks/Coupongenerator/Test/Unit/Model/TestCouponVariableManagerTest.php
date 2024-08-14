<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model;

use Aheadworks\Coupongenerator\Model\TestCouponVariableManager;
use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterfaceFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Coupongenerator\Model\TestCouponVariableManager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TestCouponVariableManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TestCouponVariableManager
     */
    private $model;

    /**
     * @var CouponVariableInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponVariableFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->couponVariableFactoryMock = $this->getMockBuilder(CouponVariableInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            TestCouponVariableManager::class,
            [
                'couponVariableFactory' => $this->couponVariableFactoryMock
            ]
        );
    }

    /**
     * Test generateCoupon method with alias specified
     */
    public function testGenerateCouponWithAlias()
    {
        $ruleId = 1;
        $alias = 'COUPON1';

        $couponVariableMock = $this->getCouponVariableMock($alias);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->generateCoupon($ruleId, $alias);
    }

    /**
     * Test generateCoupon method with no alias specified
     */
    public function testGenerateCouponWithoutAlias()
    {
        $ruleId = 1;

        $couponVariableMock = $this->getCouponVariableMock(TestCouponVariableManager::NO_ALIAS_PREFIX);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->generateCoupon($ruleId);
    }

    /**
     * Test getCouponCode method
     */
    public function testGetCouponCode()
    {
        $ruleId = 1;
        $alias = 'COUPON1';
        $couponCode = 'COUPON1-CODE';

        $couponVariableMock = $this->getCouponVariableMock($alias);
        $couponVariableMock->expects($this->once())
            ->method('getCouponCode')
            ->willReturn($couponCode);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->generateCoupon($ruleId, $alias);

        $this->assertEquals($couponCode, $this->model->getCouponCode($alias));
    }

    /**
     * Test getCouponExpirationDate method
     */
    public function testGetCouponExpirationDate()
    {
        $ruleId = 1;
        $alias = 'COUPON1';
        $couponExpirationDate = 'COUPON1-EXPIRATION-DATE';

        $couponVariableMock = $this->getCouponVariableMock($alias);
        $couponVariableMock->expects($this->once())
            ->method('getCouponExpirationDate')
            ->willReturn($couponExpirationDate);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->generateCoupon($ruleId, $alias);

        $this->assertEquals($couponExpirationDate, $this->model->getCouponExpirationDate($alias));
    }

    /**
     * Test getCouponDiscount method
     */
    public function testGetCouponDiscount()
    {
        $ruleId = 1;
        $alias = 'COUPON1';
        $discount = 'COUPON1-DISCOUNT';

        $couponVariableMock = $this->getCouponVariableMock($alias);
        $couponVariableMock->expects($this->once())
            ->method('getCouponDiscount')
            ->willReturn($discount);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->generateCoupon($ruleId, $alias);

        $this->assertEquals($discount, $this->model->getCouponDiscount($alias));
    }

    /**
     * Test getUsesPerCoupon method
     */
    public function testGetUsesPerCoupon()
    {
        $ruleId = 1;
        $alias = 'COUPON1';
        $usesPerCoupon = 'COUPON1-USES-PER-COUPON';

        $couponVariableMock = $this->getCouponVariableMock($alias);
        $couponVariableMock->expects($this->once())
            ->method('getUsesPerCoupon')
            ->willReturn($usesPerCoupon);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->generateCoupon($ruleId, $alias);

        $this->assertEquals($usesPerCoupon, $this->model->getUsesPerCoupon($alias));
    }

    /**
     * @param string $prefix
     * @return CouponVariableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getCouponVariableMock($prefix)
    {
        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock->expects($this->once())
            ->method('setCouponCode')
            ->with($prefix . '-' . TestCouponVariableManager::TEST_CODE)
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setCouponDiscount')
            ->with($prefix . '-' . TestCouponVariableManager::TEST_DISCOUNT)
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setCouponExpirationDate')
            ->with($prefix . '-' . TestCouponVariableManager::TEST_EXPIRATION_DATE)
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setUsesPerCoupon')
            ->with($prefix . '-' . TestCouponVariableManager::TEST_USES_PER_COUPON)
            ->willReturnSelf();

        return $couponVariableMock;
    }
}
