<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Controller\Adminhtml\Coupon;

use Aheadworks\Coupongenerator\Controller\Adminhtml\Coupon\MassGenerateSendCoupon;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\Coupongenerator\Api\CouponManagerInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Controller\Adminhtml\Coupon\MassGenerateSendCoupon
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassGenerateSendCouponTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MassGenerateSendCoupon
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionMock;

    /**
     * @var Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterMock;

    /**
     * @var CouponManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponManagerMock;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMockForAbstractClass(
            RequestInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getPostValue']
        );
        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock
            ]
        );

        $this->collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethods(['getAllIds'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterMock = $this->getMockBuilder(Filter::class)
            ->setMethods(['getCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->couponManagerMock = $this->getMockForAbstractClass(CouponManagerInterface::class);
        $this->customerRepositoryMock = $this->getMockForAbstractClass(CustomerRepositoryInterface::class);

        $this->controller = $objectManager->getObject(
            MassGenerateSendCoupon::class,
            [
                'context' => $this->contextMock,
                'filter' => $this->filterMock,
                'collection' => $this->collectionMock,
                'couponManager' => $this->couponManagerMock,
                'customerRepository' => $this->customerRepositoryMock
            ]
        );
    }

    /**
     * Test execute method, no rule set
     */
    public function testExecuteNoRule()
    {
        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn(['rule_id' => null]);
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('rule_id')
            ->willReturn(null);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->willReturnSelf();

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/generate')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }

    /**
     * Test execute method, successfull
     */
    public function testExecute()
    {
        $customerId = 1;
        $ruleId = 1;
        $couponCode = 'CCGZE5AIDI6UQ8EZ';
        $customerEmail = 'test@test.tt';

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn(['rule_id' => $ruleId]);
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('rule_id')
            ->willReturn($ruleId);

        $this->collectionMock->expects($this->once())
            ->method('getAllIds')
            ->willReturn([$customerId]);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($this->collectionMock)
            ->willReturn($this->collectionMock);

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $customerMock->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponResultMock->expects($this->once())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $couponResultMock->expects($this->once())
            ->method('getMessages')
            ->willReturn(
                [
                    __('Coupon %1 has been generated for %2', $couponCode, $customerEmail),
                    __('Email with coupon %1 has been send to %2', $couponCode, $customerEmail)
                ]
            );
        $this->couponManagerMock->expects($this->once())
            ->method('generateForCustomer')
            ->with($ruleId, $customerId, true)
            ->willReturn($couponResultMock);

        $this->messageManagerMock->expects($this->exactly(2))
            ->method('addSuccessMessage')
            ->willReturnSelf();

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/generate')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }
}
