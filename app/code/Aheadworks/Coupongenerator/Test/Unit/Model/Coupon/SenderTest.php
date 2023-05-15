<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Model\Coupon;

use Aheadworks\Coupongenerator\Model\Coupon\Sender;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Coupongenerator\Model\Config;
use Aheadworks\Coupongenerator\Api\CouponVariableProcessorInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Aheadworks\Coupongenerator\Api\Data\CouponInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Model\Coupon\Sender
 */
class SenderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Sender
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var TransportBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transportBuilderMock;

    /**
     * @var CouponVariableProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponVariableProcessorMock;

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
        $this->configMock = $this->getMockBuilder(Config::class)
            ->setMethods(['getEmailSender', 'getEmailSenderName', 'getEmailTemplate'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->transportBuilderMock = $this->getMockBuilder(TransportBuilder::class)
            ->setMethods([
                    'setTemplateIdentifier',
                    'setTemplateOptions',
                    'setTemplateVars',
                    'setFrom',
                    'addTo',
                    'getTransport',
                    'sendMessage'
                ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->couponVariableProcessorMock = $this->getMockForAbstractClass(CouponVariableProcessorInterface::class);
        $this->storeRepositoryMock = $this->getMockForAbstractClass(StoreRepositoryInterface::class);

        $this->model = $objectManager->getObject(
            Sender::class,
            [
                'config' => $this->configMock,
                'transportBuilder' => $this->transportBuilderMock,
                'couponVariableProcessor' => $this->couponVariableProcessorMock,
                'storeRepository' => $this->storeRepositoryMock
            ]
        );
    }

    /**
     * Test sendCoupon method
     */
    public function testSendCoupon()
    {
        $recipientName = 'Test Customer';
        $recipientEmail = 'test@test.tt';
        $storeId = 1;
        $websiteId = 2;
        $emailSender = 'general';
        $emailSenderName = 'General Contact';
        $emailTemplate = 'aw_coupongenerator_general_email_template';
        $couponCode = 'CCGZE5AIDI6UQ8EZ';

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $storeMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $storeMock->expects($this->atLeastOnce())
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $this->storeRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($storeId)
            ->willReturn($storeMock);

        $this->configMock->expects($this->once())
            ->method('getEmailSender')
            ->with($websiteId)
            ->willReturn($emailSender);
        $this->configMock->expects($this->once())
            ->method('getEmailSenderName')
            ->with($websiteId)
            ->willReturn($emailSenderName);
        $this->configMock->expects($this->once())
            ->method('getEmailTemplate')
            ->with($storeId)
            ->willReturn($emailTemplate);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponVariableMock = $this->getMockForAbstractClass(CouponVariableInterface::class);
        $couponVariableMock->expects($this->atLeastOnce())
            ->method('getCouponCode')
            ->willReturn($couponCode);
        $couponVariableMock->expects($this->atLeastOnce())
            ->method('getCouponDiscount')
            ->willReturn('%10');
        $couponVariableMock->expects($this->atLeastOnce())
            ->method('getCouponExpirationDate')
            ->willReturn('Nov 21, 2017');
        $couponVariableMock->expects($this->atLeastOnce())
            ->method('getUsesPerCoupon')
            ->willReturn('2');

        $this->couponVariableProcessorMock->expects($this->once())
            ->method('getCouponVariable')
            ->with($couponMock)
            ->willReturn($couponVariableMock);

        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateIdentifier')
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateOptions')
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateVars')
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setFrom')
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('addTo')
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('getTransport')
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('sendMessage')
            ->willReturnSelf();

        $result = true;
        try {
            $this->model->sendCoupon($couponMock, $recipientName, $recipientEmail, $storeId);
        } catch (\Exception $e) {
            $result = false;
        }
        $this->assertTrue($result);
    }
}
