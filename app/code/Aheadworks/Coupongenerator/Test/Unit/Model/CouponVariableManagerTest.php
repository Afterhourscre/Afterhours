<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model;

use Aheadworks\Coupongenerator\Model\CouponVariableManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Coupongenerator\Api\CouponManagerInterface;
use Aheadworks\Coupongenerator\Api\CouponVariableProcessorInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterfaceFactory;

/**
 * Test for \Aheadworks\Coupongenerator\Model\CouponVariableManager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponVariableManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CouponVariableManager
     */
    private $model;

    /**
     * @var CouponManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponManagerMock;

    /**
     * @var CouponVariableProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponVariableProcessorMock;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * @var StoreRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeRepositoryMock;

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

        $this->couponManagerMock = $this->getMockBuilder(CouponManagerInterface::class)
            ->getMock();
        $this->couponVariableProcessorMock = $this->getMockBuilder(CouponVariableProcessorInterface::class)
            ->getMock();
        $this->customerRepositoryMock = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->getMock();
        $this->storeRepositoryMock = $this->getMockBuilder(StoreRepositoryInterface::class)
            ->getMock();
        $this->couponVariableFactoryMock = $this->getMockBuilder(CouponVariableInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            CouponVariableManager::class,
            [
                'couponManager' => $this->couponManagerMock,
                'couponVariableProcessor' => $this->couponVariableProcessorMock,
                'customerRepository' => $this->customerRepositoryMock,
                'storeRepository' => $this->storeRepositoryMock,
                'couponVariableFactory' => $this->couponVariableFactoryMock
            ]
        );
    }

    /**
     * Test generateCoupon method, if customer id is specified
     */
    public function testGenerateCouponByCustomerId()
    {
        $ruleId = 1;
        $alias = 'COUPON1';
        $customerId = 1;
        $storeId = 1;

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->atLeastOnce())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $this->couponManagerMock
            ->expects($this->once())
            ->method('generateForCustomer')
            ->willReturn($couponGenerationResultMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $this->couponVariableProcessorMock
            ->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock, $storeId)
            ->willReturn($couponVariableMock);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->setRecipientByCustomerId($customerId);
        $this->model->setStoreId($storeId);
        $this->model->generateCoupon($ruleId, $alias);
    }

    /**
     * Test generateCoupon method, if recipient email is specified
     */
    public function testGenerateCouponByEmail()
    {
        $ruleId = 1;
        $alias = 'COUPON1';
        $email= 'test@test.tt';
        $storeId = 1;

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->atLeastOnce())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $this->couponManagerMock
            ->expects($this->once())
            ->method('generateForEmail')
            ->willReturn($couponGenerationResultMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $this->couponVariableProcessorMock
            ->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock, $storeId)
            ->willReturn($couponVariableMock);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->setRecipientByEmail($email);
        $this->model->setStoreId($storeId);
        $this->model->generateCoupon($ruleId, $alias);
    }

    /**
     * Test getCouponCode method, if customer id is specified
     */
    public function testGetCouponCode()
    {
        $ruleId = 1;
        $alias = 'COUPON1';
        $customerId = 1;
        $storeId = 1;
        $couponCode = 'CCGZE5AIDI6UQ8EZ';

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->atLeastOnce())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $this->couponManagerMock
            ->expects($this->once())
            ->method('generateForCustomer')
            ->willReturn($couponGenerationResultMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock
            ->expects($this->atLeastOnce())
            ->method('getCouponCode')
            ->willReturn($couponCode);
        $this->couponVariableProcessorMock
            ->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock, $storeId)
            ->willReturn($couponVariableMock);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->setRecipientByCustomerId($customerId);
        $this->model->setStoreId($storeId);
        $this->model->generateCoupon($ruleId, $alias);

        $this->assertEquals($couponCode, $this->model->getCouponCode($alias));
    }

    /**
     * Test getCouponCode method, if recipient email is specified with no alias specified
     */
    public function testGetCouponCodeByRecipientEmail()
    {
        $ruleId = 1;
        $email= 'test@test.tt';
        $storeId = 1;
        $couponCode = 'CCGZE5AIDI6UQ8EZ';

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->atLeastOnce())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $this->couponManagerMock
            ->expects($this->once())
            ->method('generateForEmail')
            ->willReturn($couponGenerationResultMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock
            ->expects($this->atLeastOnce())
            ->method('getCouponCode')
            ->willReturn($couponCode);
        $this->couponVariableProcessorMock
            ->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock, $storeId)
            ->willReturn($couponVariableMock);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->setRecipientByEmail($email);
        $this->model->setStoreId($storeId);
        $this->model->generateCoupon($ruleId);

        $this->assertEquals($couponCode, $this->model->getCouponCode());
    }

    /**
     * Test getCouponCode method, if customer id is specified and no proper rule is set
     */
    public function testGetCouponCodeNoProperRule()
    {
        $ruleId = 2;
        $alias = 'COUPON1';
        $customerId = 1;
        $storeId = 1;

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->atLeastOnce())
            ->method('getCoupon')
            ->willReturn(null);
        $this->couponManagerMock
            ->expects($this->once())
            ->method('generateForCustomer')
            ->willReturn($couponGenerationResultMock);

        $this->model->setRecipientByCustomerId($customerId);
        $this->model->setStoreId($storeId);
        $this->model->generateCoupon($ruleId, $alias);

        $this->assertNull($this->model->getCouponCode($alias));
    }

    /**
     * Test getCouponExpirationDate method, if customer id is specified
     */
    public function testGetCouponExpirationDate()
    {
        $ruleId = 1;
        $alias = 'COUPON1';
        $customerId = 1;
        $storeId = 1;
        $expirationDate = 'Nov 21, 2020';

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->atLeastOnce())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $this->couponManagerMock
            ->expects($this->once())
            ->method('generateForCustomer')
            ->willReturn($couponGenerationResultMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock
            ->expects($this->atLeastOnce())
            ->method('getCouponExpirationDate')
            ->willReturn($expirationDate);
        $this->couponVariableProcessorMock
            ->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock, $storeId)
            ->willReturn($couponVariableMock);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->setRecipientByCustomerId($customerId);
        $this->model->setStoreId($storeId);
        $this->model->generateCoupon($ruleId, $alias);

        $this->assertEquals($expirationDate, $this->model->getCouponExpirationDate($alias));
    }

    /**
     * Test getCouponExpirationDate method, if recipient email is specified with no alias specified
     */
    public function testGetCouponExpirationDateByRecipientEmail()
    {
        $ruleId = 1;
        $email= 'test@test.tt';
        $storeId = 1;
        $expirationDate = 'Nov 21, 2020';

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->atLeastOnce())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $this->couponManagerMock
            ->expects($this->once())
            ->method('generateForEmail')
            ->willReturn($couponGenerationResultMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock
            ->expects($this->atLeastOnce())
            ->method('getCouponExpirationDate')
            ->willReturn($expirationDate);
        $this->couponVariableProcessorMock
            ->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock, $storeId)
            ->willReturn($couponVariableMock);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->setRecipientByEmail($email);
        $this->model->setStoreId($storeId);
        $this->model->generateCoupon($ruleId);

        $this->assertEquals($expirationDate, $this->model->getCouponExpirationDate());
    }

    /**
     * Test getCouponDiscount method, if customer id is specified
     */
    public function testGetCouponDiscount()
    {
        $ruleId = 1;
        $alias = 'COUPON1';
        $customerId = 1;
        $storeId = 1;
        $discount = '10%';

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->atLeastOnce())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $this->couponManagerMock
            ->expects($this->once())
            ->method('generateForCustomer')
            ->willReturn($couponGenerationResultMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock
            ->expects($this->atLeastOnce())
            ->method('getCouponDiscount')
            ->willReturn($discount);
        $this->couponVariableProcessorMock
            ->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock, $storeId)
            ->willReturn($couponVariableMock);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->setRecipientByCustomerId($customerId);
        $this->model->setStoreId($storeId);
        $this->model->generateCoupon($ruleId, $alias);

        $this->assertEquals($discount, $this->model->getCouponDiscount($alias));
    }

    /**
     * Test getCouponDiscount method, if recipient email is specified with no alias specified
     */
    public function testGetCouponDiscountByRecipientEmail()
    {
        $ruleId = 1;
        $email= 'test@test.tt';
        $storeId = 1;
        $discount = '10%';

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->atLeastOnce())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $this->couponManagerMock
            ->expects($this->once())
            ->method('generateForEmail')
            ->willReturn($couponGenerationResultMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock
            ->expects($this->atLeastOnce())
            ->method('getCouponDiscount')
            ->willReturn($discount);
        $this->couponVariableProcessorMock
            ->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock, $storeId)
            ->willReturn($couponVariableMock);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->setRecipientByEmail($email);
        $this->model->setStoreId($storeId);
        $this->model->generateCoupon($ruleId);

        $this->assertEquals($discount, $this->model->getCouponDiscount());
    }

    /**
     * Test getUsesPerCoupon method, if customer id is specified
     */
    public function testGetUsesPerCoupon()
    {
        $ruleId = 1;
        $alias = 'COUPON1';
        $customerId = 1;
        $storeId = 1;
        $usesPerCoupon = 2;

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->atLeastOnce())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $this->couponManagerMock
            ->expects($this->once())
            ->method('generateForCustomer')
            ->willReturn($couponGenerationResultMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock
            ->expects($this->atLeastOnce())
            ->method('getUsesPerCoupon')
            ->willReturn($usesPerCoupon);
        $this->couponVariableProcessorMock
            ->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock, $storeId)
            ->willReturn($couponVariableMock);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->setRecipientByCustomerId($customerId);
        $this->model->setStoreId($storeId);
        $this->model->generateCoupon($ruleId, $alias);

        $this->assertEquals($usesPerCoupon, $this->model->getUsesPerCoupon($alias));
    }

    /**
     * Test getUsesPerCoupon method, if recipient email is specified with no alias specified
     */
    public function testGetUsesPerCouponByRecipientEmail()
    {
        $ruleId = 1;
        $email= 'test@test.tt';
        $storeId = 1;
        $usesPerCoupon = 2;

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->atLeastOnce())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $this->couponManagerMock
            ->expects($this->once())
            ->method('generateForEmail')
            ->willReturn($couponGenerationResultMock);

        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock
            ->expects($this->atLeastOnce())
            ->method('getUsesPerCoupon')
            ->willReturn($usesPerCoupon);
        $this->couponVariableProcessorMock
            ->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock, $storeId)
            ->willReturn($couponVariableMock);
        $this->couponVariableFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponVariableMock);

        $this->model->setRecipientByEmail($email);
        $this->model->setStoreId($storeId);
        $this->model->generateCoupon($ruleId);

        $this->assertEquals($usesPerCoupon, $this->model->getUsesPerCoupon());
    }

    /**
     * Test setRecipientByCustomerId method
     */
    public function testSetRecipientByCustomerId()
    {
        $customerId = 1;

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $this->model->setRecipientByCustomerId($customerId);
    }

    /**
     * Test setStoreId method
     */
    public function testSetStoreId()
    {
        $storeId = 1;

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $this->model->setStoreId($storeId);
    }
}
