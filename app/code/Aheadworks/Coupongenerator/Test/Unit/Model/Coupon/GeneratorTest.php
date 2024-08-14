<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model\Coupon;

use Aheadworks\Coupongenerator\Model\Coupon\Generator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Coupongenerator\Model\SalesruleRepository;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterface;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterfaceFactory;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterface;
use Magento\SalesRule\Api\CouponManagementInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\SalesRule\Api\Data\CouponInterface as MagentoCouponInterface;
use Magento\SalesRule\Api\Data\CouponSearchResultInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponInterfaceFactory;
use Aheadworks\Coupongenerator\Api\Data\CouponInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Model\Coupon\Generator
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Generator
     */
    private $model;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var SalesruleRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleRepositoryMock;

    /**
     * @var CouponGenerationSpecInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponGenerationSpecInterfaceFactoryMock;

    /**
     * @var CouponManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponManagementMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var CouponRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponRepositoryMock;

    /**
     * @var CouponInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponInterfaceFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['save'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->salesruleRepositoryMock = $this->getMockBuilder(SalesruleRepository::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->couponGenerationSpecInterfaceFactoryMock = $this->getMockBuilder(
            CouponGenerationSpecInterfaceFactory::class
        )
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->couponManagementMock = $this->getMockForAbstractClass(CouponManagementInterface::class);
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['addFilter', 'create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->couponRepositoryMock = $this->getMockForAbstractClass(CouponRepositoryInterface::class);
        $this->couponInterfaceFactoryMock = $this->getMockBuilder(CouponInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            Generator::class,
            [
                'entityManager' => $this->entityManagerMock,
                'salesruleRepository' => $this->salesruleRepositoryMock,
                'couponGenerationSpecInterfaceFactory' => $this->couponGenerationSpecInterfaceFactoryMock,
                'couponManagement' => $this->couponManagementMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'couponRepository' => $this->couponRepositoryMock,
                'couponInterfaceFactory' => $this->couponInterfaceFactoryMock
            ]
        );
    }

    /**
     * Test generateCoupon method, coupon is generated successfully
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGenerateCoupon()
    {
        $ruleId = 1;
        $customerId = 1;
        $customerEmail = 'test@test.tt';
        $adminUserId = 1;
        $couponCode = 'CCGZE5AIDI6UQ8EZ';
        $couponId = 1;

        $salesRuleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $this->salesruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($ruleId)
            ->willReturn($salesRuleMock);

        $couponGenerationSpecMock = $this->getMockForAbstractClass(CouponGenerationSpecInterface::class);
        $couponGenerationSpecMock->expects($this->once())
            ->method('setRuleId')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setFormat')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setQuantity')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setLength')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setPrefix')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setSuffix')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setDelimiterAtEvery')
            ->willReturnSelf();
        $this->couponGenerationSpecInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponGenerationSpecMock);

        $this->couponManagementMock->expects($this->once())
            ->method('generate')
            ->with($couponGenerationSpecMock)
            ->willReturn([$couponCode]);

        $seachCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($seachCriteriaMock);

        $couponSearchResultMock = $this->getMockForAbstractClass(CouponSearchResultInterface::class);
        $couponSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn(
                [
                    ['coupon_id' => $couponId]
                ]
            );
        $couponMock = $this->getMockForAbstractClass(MagentoCouponInterface::class);
        $couponMock->expects($this->once())
            ->method('getCouponId')
            ->willReturn($couponId);
        $this->couponRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($seachCriteriaMock)
            ->willReturn($couponSearchResultMock);
        $this->couponRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($couponId)
            ->willReturn($couponMock);

        $couponDataObjectMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponDataObjectMock->expects($this->once())
            ->method('setCouponId')
            ->willReturnSelf();
        $couponDataObjectMock->expects($this->once())
            ->method('setIsDeactivated')
            ->willReturnSelf();
        $couponDataObjectMock->expects($this->once())
            ->method('setAdminUserId')
            ->willReturnSelf();
        $couponDataObjectMock->expects($this->once())
            ->method('setRecipientEmail')
            ->willReturnSelf();
        $couponDataObjectMock->expects($this->once())
            ->method('setCustomerId')
            ->willReturnSelf();
        $this->couponInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponDataObjectMock);

        $this->assertEquals(
            $couponMock,
            $this->model->generateCoupon($ruleId, $customerId, $customerEmail, $adminUserId)
        );
    }

    /**
     * Test generateCoupon method, no coupon is generated
     */
    public function testGenerateCouponNoCoupon()
    {
        $ruleId = 1;
        $customerId = 1;
        $customerEmail = 'test@test.tt';
        $adminUserId = 1;
        $couponCode = 'CCGZE5AIDI6UQ8EZ';

        $salesRuleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $this->salesruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($ruleId)
            ->willReturn($salesRuleMock);

        $couponGenerationSpecMock = $this->getMockForAbstractClass(CouponGenerationSpecInterface::class);
        $couponGenerationSpecMock->expects($this->once())
            ->method('setRuleId')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setFormat')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setQuantity')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setLength')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setPrefix')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setSuffix')
            ->willReturnSelf();
        $couponGenerationSpecMock->expects($this->once())
            ->method('setDelimiterAtEvery')
            ->willReturnSelf();
        $this->couponGenerationSpecInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponGenerationSpecMock);

        $this->couponManagementMock->expects($this->once())
            ->method('generate')
            ->with($couponGenerationSpecMock)
            ->willReturn([$couponCode]);

        $seachCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($seachCriteriaMock);

        $couponSearchResultMock = $this->getMockForAbstractClass(CouponSearchResultInterface::class);
        $couponSearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([]);
        $this->couponRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($seachCriteriaMock)
            ->willReturn($couponSearchResultMock);

        $this->assertEquals(
            false,
            $this->model->generateCoupon($ruleId, $customerId, $customerEmail, $adminUserId)
        );
    }
}
