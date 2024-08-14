<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model;

use Aheadworks\Coupongenerator\Model\CouponManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Aheadworks\Coupongenerator\Model\SalesruleRepository;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterface;
use Magento\User\Model\UserFactory;
use Aheadworks\Coupongenerator\Model\Coupon\Generator as CouponGenerator;
use Aheadworks\Coupongenerator\Model\Coupon\Sender as CouponSender;
use Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterfaceFactory;
use Magento\SalesRule\Api\Data\CouponInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Model\CouponManager
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CouponManager
     */
    private $model;

    /**
     * @var UserFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $userFactoryMock;

    /**
     * @var CouponGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponGeneratorMock;

    /**
     * @var CouponSender|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponSenderMock;

    /**
     * @var CouponGenerationResultInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponGenerationResultFactoryMock;

    /**
     * @var SalesruleRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleRepositoryMock;

    /**
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRepositoryMock;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * @var StoreRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->userFactoryMock = $this->getMockBuilder(UserFactory::class)
            ->setMethods(['create', 'getCollection', 'addFieldToFilter', 'getFirstItem', 'getUserId'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->couponGenerationResultFactoryMock = $this->getMockBuilder(CouponGenerationResultInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->couponGeneratorMock = $this->getMockBuilder(CouponGenerator::class)
            ->setMethods(['generateCoupon'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->couponSenderMock = $this->getMockBuilder(CouponSender::class)
            ->setMethods(['sendCoupon'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->salesruleRepositoryMock = $this->getMockBuilder(SalesruleRepository::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->ruleRepositoryMock = $this->getMockBuilder(RuleRepositoryInterface::class)
            ->getMock();
        $this->customerRepositoryMock = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->getMock();
        $this->storeRepositoryMock = $this->getMockBuilder(StoreRepositoryInterface::class)
            ->getMock();

        $this->model = $objectManager->getObject(
            CouponManager::class,
            [
                'userFactory' => $this->userFactoryMock,
                'couponGenerationResultFactory' => $this->couponGenerationResultFactoryMock,
                'couponGenerator' => $this->couponGeneratorMock,
                'couponSender' => $this->couponSenderMock,
                'salesruleRepository' => $this->salesruleRepositoryMock,
                'ruleRepository' => $this->ruleRepositoryMock,
                'customerRepository' => $this->customerRepositoryMock,
                'storeRepository' => $this->storeRepositoryMock
            ]
        );
    }

    /**
     * Test generateForEmail method
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGenerateForEmail()
    {
        $salesruleId = 1;
        $magentoRuleId = 2;
        $email = 'test@test.tt';
        $storeId = 1;
        $websiteId = 2;
        $isSendEmail = true;
        $adminUseruserId = 2;
        $couponCode = 'CCGZE5AIDI6UQ8EZ';

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleMock
            ->expects($this->atLeastOnce())
            ->method('getRuleId')
            ->willReturn($magentoRuleId);
        $this->salesruleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturn($salesruleMock);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock
            ->expects($this->once())
            ->method('getWebsiteIds')
            ->willReturn([$websiteId]);
        $ruleMock
            ->expects($this->once())
            ->method('getIsActive')
            ->willReturn(true);
        $this->ruleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('getById')
            ->with($magentoRuleId)
            ->willReturn($ruleMock);

        $storeMock = $this->getMockForAbstractClass(
            StoreInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getId', 'getWebsiteId', 'isActive']
        );
        $storeMock
            ->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $storeMock
            ->expects($this->once())
            ->method('isActive')
            ->willReturn(true);
        $storeMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);

        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->willReturn([$storeMock]);

        $this->userFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturnSelf();
        $this->userFactoryMock
            ->expects($this->once())
            ->method('getCollection')
            ->willReturnSelf();
        $this->userFactoryMock
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->willReturnSelf();
        $this->userFactoryMock
            ->expects($this->once())
            ->method('getFirstItem')
            ->willReturnSelf();
        $this->userFactoryMock
            ->expects($this->once())
            ->method('getUserId')
            ->willReturn($adminUseruserId);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponMock
            ->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn($couponCode);
        $this->couponGeneratorMock
            ->expects($this->once())
            ->method('generateCoupon')
            ->with($salesruleId, null, $email, $adminUseruserId)
            ->willReturn($couponMock);

        $this->couponSenderMock
            ->expects($this->once())
            ->method('sendCoupon')
            ->with($couponMock, '', $email, $storeId);

        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->once())
            ->method('setCoupon')
            ->willReturnSelf();
        $couponGenerationResultMock
            ->expects($this->once())
            ->method('setMessages')
            ->willReturnSelf();
        $this->couponGenerationResultFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponGenerationResultMock);

        $this->assertEquals(
            $couponGenerationResultMock,
            $this->model->generateForEmail($salesruleId, $email, $isSendEmail)
        );
    }

    /**
     * Test generateForEmail method, rule is not active
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Rule Test is not active
     */
    public function testGenerateForEmailRuleInactive()
    {
        $salesruleId = 1;
        $magentoRuleId = 2;
        $magentoRuleName = 'Test';
        $email = 'test@test.tt';
        $storeId = 1;
        $websiteId = 2;
        $isSendEmail = true;

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleMock
            ->expects($this->atLeastOnce())
            ->method('getRuleId')
            ->willReturn($magentoRuleId);
        $this->salesruleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturn($salesruleMock);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock
            ->expects($this->once())
            ->method('getWebsiteIds')
            ->willReturn([$websiteId]);
        $ruleMock
            ->expects($this->once())
            ->method('getIsActive')
            ->willReturn(false);
        $ruleMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn($magentoRuleName);
        $this->ruleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('getById')
            ->with($magentoRuleId)
            ->willReturn($ruleMock);

        $storeMock = $this->getMockForAbstractClass(
            StoreInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getId', 'getWebsiteId', 'isActive']
        );
        $storeMock
            ->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $storeMock
            ->expects($this->once())
            ->method('isActive')
            ->willReturn(true);
        $storeMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);

        $this->storeRepositoryMock
            ->expects($this->once())
            ->method('getList')
            ->willReturn([$storeMock]);

        $this->model->generateForEmail($salesruleId, $email, $isSendEmail);
    }

    /**
     * Test generateForCustomer method
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGenerateForCustomer()
    {
        $salesruleId = 1;
        $magentoRuleId = 2;
        $customerId = 1;
        $firstname = 'First Name';
        $email = 'test@test.tt';
        $storeId = 1;
        $websiteId = 2;
        $isSendEmail = true;
        $adminUseruserId = 2;
        $couponCode = 'CCGZE5AIDI6UQ8EZ';

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $customerMock
            ->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);
        $customerMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);
        $customerMock
            ->expects($this->once())
            ->method('getFirstname')
            ->willReturn($firstname);
        $customerMock
            ->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $customerMock
            ->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleMock
            ->expects($this->atLeastOnce())
            ->method('getRuleId')
            ->willReturn($magentoRuleId);
        $this->salesruleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturn($salesruleMock);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock
            ->expects($this->once())
            ->method('getRuleId')
            ->willReturn($magentoRuleId);
        $ruleMock
            ->expects($this->once())
            ->method('getWebsiteIds')
            ->willReturn([$websiteId]);
        $ruleMock
            ->expects($this->once())
            ->method('getIsActive')
            ->willReturn(true);
        $this->ruleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('getById')
            ->with($magentoRuleId)
            ->willReturn($ruleMock);

        $this->userFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturnSelf();
        $this->userFactoryMock
            ->expects($this->once())
            ->method('getCollection')
            ->willReturnSelf();
        $this->userFactoryMock
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->willReturnSelf();
        $this->userFactoryMock
            ->expects($this->once())
            ->method('getFirstItem')
            ->willReturnSelf();
        $this->userFactoryMock
            ->expects($this->once())
            ->method('getUserId')
            ->willReturn($adminUseruserId);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponMock
            ->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn($couponCode);
        $this->couponGeneratorMock
            ->expects($this->once())
            ->method('generateCoupon')
            ->with($salesruleId, $customerId, $email, $adminUseruserId)
            ->willReturn($couponMock);

        $this->couponSenderMock
            ->expects($this->once())
            ->method('sendCoupon')
            ->with($couponMock, $firstname, $email, $storeId);

        $couponGenerationResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponGenerationResultMock
            ->expects($this->once())
            ->method('setCoupon')
            ->willReturnSelf();
        $couponGenerationResultMock
            ->expects($this->once())
            ->method('setMessages')
            ->willReturnSelf();
        $this->couponGenerationResultFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($couponGenerationResultMock);

        $this->assertEquals(
            $couponGenerationResultMock,
            $this->model->generateForCustomer($salesruleId, $customerId, $isSendEmail)
        );
    }

    /**
     * Test generateForCustomer method, if rule is not active
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Rule Test is not active
     */
    public function testGenerateForCustomerRileInactive()
    {
        $salesruleId = 1;
        $magentoRuleId = 2;
        $magentoRuleName = 'Test';
        $customerId = 1;
        $firstname = 'First Name';
        $email = 'test@test.tt';
        $storeId = 1;
        $websiteId = 2;
        $isSendEmail = true;

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $customerMock
            ->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);
        $customerMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);
        $customerMock
            ->expects($this->once())
            ->method('getFirstname')
            ->willReturn($firstname);
        $customerMock
            ->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $customerMock
            ->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $this->customerRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleMock
            ->expects($this->atLeastOnce())
            ->method('getRuleId')
            ->willReturn($magentoRuleId);
        $this->salesruleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturn($salesruleMock);

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock
            ->expects($this->once())
            ->method('getRuleId')
            ->willReturn($magentoRuleId);
        $ruleMock
            ->expects($this->once())
            ->method('getWebsiteIds')
            ->willReturn([$websiteId]);
        $ruleMock
            ->expects($this->once())
            ->method('getIsActive')
            ->willReturn(false);
        $ruleMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn($magentoRuleName);
        $this->ruleRepositoryMock
            ->expects($this->atLeastOnce())
            ->method('getById')
            ->with($magentoRuleId)
            ->willReturn($ruleMock);

        $this->model->generateForCustomer($salesruleId, $customerId, $isSendEmail);
    }
}
