<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Controller\Adminhtml\Rule;

use Aheadworks\Coupongenerator\Controller\Adminhtml\Rule\Delete;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Aheadworks\Coupongenerator\Model\SalesruleRepository;
use Aheadworks\Coupongenerator\Api\Data\SalesruleInterface;

/**
 * Test for \Aheadworks\Coupongenerator\Controller\Adminhtml\Rule\Delete
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DeleteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Delete
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
     * @var SalesruleRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $salesruleRepositoryMock;

    /**
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $magentoSalesRuleRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
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

        $this->salesruleRepositoryMock = $this->getMockBuilder(SalesruleRepository::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->magentoSalesRuleRepositoryMock = $this->getMockForAbstractClass(RuleRepositoryInterface::class);

        $this->controller = $objectManager->getObject(
            Delete::class,
            [
                'context' => $this->contextMock,
                'magentoSalesRuleRepository' => $this->magentoSalesRuleRepositoryMock,
                'salesruleRepository' => $this->salesruleRepositoryMock
            ]
        );
    }

    /**
     * Test execute method, if no id specified
     */
    public function testExecuteNoId()
    {
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn(null);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('Rule can\'t be deleted'));

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

    /**
     * Test execute method, if salesrule no longer exists
     */
    public function testExecuteSalesruleNoLongerExists()
    {
        $salesruleId = 1;
        $exception = new NoSuchEntityException;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($salesruleId);

        $this->salesruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($salesruleId)
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->exactly(2))
            ->method('addErrorMessage');

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

    /**
     * Test execute method, if Magento rule no longer exists
     */
    public function testExecuteMagentoRuleNoLongerExists()
    {
        $salesruleId = 1;
        $magentoRuleId = 2;
        $exception = new NoSuchEntityException;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($salesruleId);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleMock->expects($this->once())
            ->method('getRuleId')
            ->willReturn($magentoRuleId);

        $this->salesruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($salesruleId)
            ->willReturn($salesruleMock);

        $this->magentoSalesRuleRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($magentoRuleId)
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->exactly(2))
            ->method('addErrorMessage');

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

    /**
     * Test execute method, if rule exists
     */
    public function testExecuteRuleExists()
    {
        $salesruleId = 1;
        $magentoRuleId = 2;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($salesruleId);

        $salesruleMock = $this->getMockForAbstractClass(SalesruleInterface::class);
        $salesruleMock->expects($this->once())
            ->method('getRuleId')
            ->willReturn($magentoRuleId);

        $this->salesruleRepositoryMock->expects($this->once())
            ->method('get')
            ->willReturn($salesruleMock);

        $this->magentoSalesRuleRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($magentoRuleId)
            ->willReturn(true);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('Rule was successfully deleted'));

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
