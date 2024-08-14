<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Controller\Adminhtml\Coupons;

use Aheadworks\Coupongenerator\Controller\Adminhtml\Coupons\MassDelete;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Aheadworks\Coupongenerator\Model\ResourceModel\Coupon\CollectionFactory;
use Aheadworks\Coupongenerator\Model\ResourceModel\Coupon\Collection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Coupongenerator\Api\Data\CouponInterfaceFactory;
use Aheadworks\Coupongenerator\Api\Data\CouponInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Controller\Adminhtml\Coupons\MassDelete
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassDeleteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MassDelete
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterMock;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var CouponInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $couponInterfaceFactoryMock;

    /**
     * @var CouponRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
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

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock
            ]
        );

        $this->collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->filterMock = $this->getMockBuilder(Filter::class)
            ->setMethods(['getCollection'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['load'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->couponInterfaceFactoryMock = $this->getMockBuilder(CouponInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->couponRepositoryMock = $this->getMockForAbstractClass(CouponRepositoryInterface::class);

        $this->controller = $objectManager->getObject(
            MassDelete::class,
            [
                'context' => $this->contextMock,
                'filter' => $this->filterMock,
                'collectionFactory' => $this->collectionFactoryMock,
                'entityManager' => $this->entityManagerMock,
                'couponInterfaceFactory' => $this->couponInterfaceFactoryMock,
                'couponRepository' => $this->couponRepositoryMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $couponId = 1;
        $magentoCouponId = 2;
        $count = 1;

        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethods(['getAllIds'])
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('getAllIds')
            ->willReturn([$couponId]);
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($collectionMock)
            ->willReturn($collectionMock);

        $couponMock = $this->getMockForAbstractClass(CouponInterface::class);
        $couponMock->expects($this->once())
            ->method('getCouponId')
            ->willReturn($magentoCouponId);
        $this->couponInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($couponMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($couponMock, $couponId)
            ->willReturn($couponMock);

        $this->couponRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($magentoCouponId)
            ->willReturn(true);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('A total of %1 coupon(s) have been deleted', $count))
            ->willReturnSelf();

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->setMethods(['setPath'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/index')
            ->willReturnSelf();
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirectMock);

        $this->assertSame($resultRedirectMock, $this->controller->execute());
    }
}
