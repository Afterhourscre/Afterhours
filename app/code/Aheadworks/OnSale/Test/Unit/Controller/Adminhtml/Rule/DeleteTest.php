<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Rule;

use Aheadworks\OnSale\Controller\Adminhtml\Rule\Delete;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Api\RuleRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class DeleteTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Rule
 */
class DeleteTest extends TestCase
{
    /**
     * @var Delete
     */
    private $controller;

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
     * @var RuleRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleRepositoryMock;

    /**
     * @var Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    private $redirectMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->resultRedirectFactoryMock = $this->createPartialMock(RedirectFactory::class, ['create']);
        $this->ruleRepositoryMock = $this->getMockForAbstractClass(RuleRepositoryInterface::class);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $context = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
                'messageManager' => $this->messageManagerMock
            ]
        );

        $this->redirectMock = $this->createPartialMock(Redirect::class, ['setPath']);
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->redirectMock);

        $this->redirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->controller = $objectManager->getObject(
            Delete::class,
            [
                'context' => $context,
                'ruleRepository' => $this->ruleRepositoryMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $ruleId = 1;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($ruleId);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($ruleId);
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('You deleted the rule.'));

        $this->assertSame($this->redirectMock, $this->controller->execute());
    }

    /**
     * Test execute method on exception
     */
    public function testExecuteOnException()
    {
        $ruleId = 1;
        $exceptionMessage = 'exception message';
        $exception = new \Exception($exceptionMessage);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($ruleId);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($ruleId)
            ->willThrowException($exception);
        $this->messageManagerMock->expects($this->exactly(2))
            ->method('addErrorMessage')
            ->withConsecutive(
                [$exceptionMessage],
                [__('Something went wrong while deleting the rule.')]
            );

        $this->assertSame($this->redirectMock, $this->controller->execute());
    }

    /**
     * Test execute method on Rule id is empty
     */
    public function testExecuteOnRuleIdIsEmpty()
    {
        $ruleId = null;

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($ruleId);
        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->withConsecutive(__('Something went wrong while deleting the rule.'));

        $this->assertSame($this->redirectMock, $this->controller->execute());
    }
}
