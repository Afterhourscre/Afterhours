<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model;

use Aheadworks\Coupongenerator\Model\CouponVariableProcessor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterfaceFactory;
use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\Framework\Locale\CurrencyInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Model\CouponVariableProcessor
 */
class CouponVariableProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CouponVariableProcessor
     */
    private $model;

    /**
     * @var StoreRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeRepositoryMock;

    /**
     * @var CouponVariableInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponVariableFactoryMock;

    /**
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRepositoryMock;

    /**
     * @var CurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeCurrencyMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->storeRepositoryMock = $this->getMockBuilder(StoreRepositoryInterface::class)
            ->getMock();
        $this->couponVariableFactoryMock = $this->getMockBuilder(CouponVariableInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleRepositoryMock = $this->getMockBuilder(RuleRepositoryInterface::class)
            ->getMock();
        $this->localeCurrencyMock = $this->getMockForAbstractClass(
            CurrencyInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['toCurrency']
        );

        $this->model = $objectManager->getObject(
            CouponVariableProcessor::class,
            [
                'storeRepository' => $this->storeRepositoryMock,
                'couponVariableFactory' => $this->couponVariableFactoryMock,
                'ruleRepository' => $this->ruleRepositoryMock,
                'localeCurrency' => $this->localeCurrencyMock
            ]
        );
    }

    /**
     * Test getCouponVariable method, rule DISCOUNT_ACTION_BY_PERCENT
     */
    public function testGetCouponVariableByPercent()
    {
        $storeId = 1;
        $ruleId = 2;
        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponMock->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn('CCGZE5AIDI6UQ8EZ');
        $couponMock->expects($this->atLeastOnce())
            ->method('getRuleId')
            ->willReturn($ruleId);
        $couponMock->expects($this->atLeastOnce())
            ->method('getExpirationDate')
            ->willReturn('2017-09-19 00:00:00');
        $couponMock->expects($this->atLeastOnce())
            ->method('getUsageLimit')
            ->willReturn(1);

        $storeMock = $this->getMockForAbstractClass(
            StoreInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getCurrentCurrencyCode']
        );
        $storeMock->expects($this->atLeastOnce())
            ->method('getCurrentCurrencyCode')
            ->willReturn('$');

        $this->storeRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock->expects($this->once())
            ->method('setCouponCode')
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setCouponExpirationDate')
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setCouponDiscount')
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setUsesPerCoupon')
            ->willReturnSelf();
        $this->couponVariableFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->atLeastOnce())
            ->method('getSimpleAction')
            ->willReturn(RuleInterface::DISCOUNT_ACTION_BY_PERCENT);
        $ruleMock->expects($this->atLeastOnce())
            ->method('getDiscountAmount')
            ->willReturn(10);
        $this->ruleRepositoryMock->expects($this->atLeastOnce())
            ->method('getById')
            ->with($ruleId)
            ->willReturn($ruleMock);

        $this->assertEquals($couponVariableMock, $this->model->getCouponVariable($couponMock, $storeId));
    }

    /**
     * Test getCouponVariable method, rule DISCOUNT_ACTION_FIXED_AMOUNT
     */
    public function testGetCouponVariableFixedAmount()
    {
        $storeId = 1;
        $ruleId = 2;
        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponMock->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn('CCGZE5AIDI6UQ8EZ');
        $couponMock->expects($this->atLeastOnce())
            ->method('getRuleId')
            ->willReturn($ruleId);
        $couponMock->expects($this->atLeastOnce())
            ->method('getExpirationDate')
            ->willReturn('2017-09-19 00:00:00');
        $couponMock->expects($this->atLeastOnce())
            ->method('getUsageLimit')
            ->willReturn(1);

        $storeMock = $this->getMockForAbstractClass(
            StoreInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getCurrentCurrencyCode', 'getBaseCurrency', 'getRate']
        );
        $storeMock->expects($this->atLeastOnce())
            ->method('getCurrentCurrencyCode')
            ->willReturn('$');
        $storeMock->expects($this->atLeastOnce())
            ->method('getBaseCurrency')
            ->willReturnSelf();
        $storeMock->expects($this->atLeastOnce())
            ->method('getRate')
            ->willReturn(1);

        $this->storeRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock->expects($this->once())
            ->method('setCouponCode')
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setCouponExpirationDate')
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setCouponDiscount')
            ->willReturnSelf();
        $couponVariableMock->expects($this->once())
            ->method('setUsesPerCoupon')
            ->willReturnSelf();
        $this->couponVariableFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->atLeastOnce())
            ->method('getSimpleAction')
            ->willReturn(RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT);
        $ruleMock->expects($this->atLeastOnce())
            ->method('getDiscountAmount')
            ->willReturn(10);
        $this->ruleRepositoryMock->expects($this->atLeastOnce())
            ->method('getById')
            ->with($ruleId)
            ->willReturn($ruleMock);

        $this->localeCurrencyMock->expects($this->atLeastOnce())
            ->method('getCurrency')
            ->willReturnSelf();
        $this->localeCurrencyMock->expects($this->atLeastOnce())
            ->method('toCurrency')
            ->willReturnSelf();

        $this->assertEquals($couponVariableMock, $this->model->getCouponVariable($couponMock, $storeId));
    }
}
