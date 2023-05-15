<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Controller\Adminhtml\Coupon;

use Aheadworks\Coupongenerator\Controller\Adminhtml\Coupon\GenerateCoupon;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Aheadworks\Coupongenerator\Api\CouponManagerInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Controller\Adminhtml\Coupon\GenerateCoupon
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GenerateCouponTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var GenerateCoupon
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

        $this->couponManagerMock = $this->getMockForAbstractClass(CouponManagerInterface::class);
        $this->customerRepositoryMock = $this->getMockForAbstractClass(CustomerRepositoryInterface::class);

        $this->controller = $objectManager->getObject(
            GenerateCoupon::class,
            [
                'context' => $this->contextMock,
                'couponManager' => $this->couponManagerMock,
                'customerRepository' => $this->customerRepositoryMock
            ]
        );
    }

    /**
     * Test execute method, no rule is set
     */
    public function testExecuteNoRule()
    {
        $recipientEmail = 'test@test.tt';
        $sendEmailToRecipient = true;

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn(
                [
                    'recipient_email' => $recipientEmail,
                    'send_email_to_recipient' => $sendEmailToRecipient
                ]
            );

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
     * Test execute method, email is not valid
     */
    public function testExecuteNotValidEmail()
    {
        $recipientEmail = 'test@test@test.tt';
        $ruleId = 1;
        $sendEmailToRecipient = true;

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn(
                [
                    'recipient_email' => $recipientEmail,
                    'rule_id' => $ruleId,
                    'send_email_to_recipient' => $sendEmailToRecipient
                ]
            );

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
     * Test execute method, send to registered customer
     */
    public function testExecuteRegisteredCustomerSuccessful()
    {
        $recipientEmail = 'test@test.tt';
        $ruleId = 1;
        $customerId = 1;
        $sendEmailToRecipient = true;
        $couponCode = 'CCGZE5AIDI6UQ8EZ';

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn(
                [
                    'recipient_email' => $recipientEmail,
                    'rule_id' => $ruleId,
                    'send_email_to_recipient' => $sendEmailToRecipient
                ]
            );

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $customerMock->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);
        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($recipientEmail)
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
                    __('Coupon %1 has been generated for %2', $couponCode, $recipientEmail),
                    __('Email with coupon %1 has been send to %2', $couponCode, $recipientEmail)
                ]
            );
        $this->couponManagerMock->expects($this->once())
            ->method('generateForCustomer')
            ->with($ruleId, $customerId, $sendEmailToRecipient)
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

    /**
     * Test execute method, send to unregistered customer
     */
    public function testExecuteUnregisteredCustomerSuccessful()
    {
        $recipientEmail = 'test@test.tt';
        $ruleId = 1;
        $sendEmailToRecipient = true;
        $couponCode = 'CCGZE5AIDI6UQ8EZ';

        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn(
                [
                    'recipient_email' => $recipientEmail,
                    'rule_id' => $ruleId,
                    'send_email_to_recipient' => $sendEmailToRecipient
                ]
            );

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($recipientEmail)
            ->willThrowException(new NoSuchEntityException());

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponResultMock = $this->getMockForAbstractClass(CouponGenerationResultInterface::class);
        $couponResultMock->expects($this->once())
            ->method('getCoupon')
            ->willReturn($couponMock);
        $couponResultMock->expects($this->once())
            ->method('getMessages')
            ->willReturn(
                [
                    __('Coupon %1 has been generated for %2', $couponCode, $recipientEmail),
                    __('Email with coupon %1 has been send to %2', $couponCode, $recipientEmail)
                ]
            );
        $this->couponManagerMock->expects($this->once())
            ->method('generateForEmail')
            ->with($ruleId, $recipientEmail, $sendEmailToRecipient)
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
