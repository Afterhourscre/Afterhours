<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Rule;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\RuleRepositoryInterface;
use Aheadworks\OnSale\Controller\Adminhtml\Rule\Edit;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;

/**
 * Class EditTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Rule
 */
class EditTest extends TestCase
{
    /**
     * @var Edit
     */
    private $controller;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageFactoryMock;

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
     * @var Page|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageMock;

    /**
     * @var Title|\PHPUnit_Framework_MockObject_MockObject
     */
    private $titleMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->resultPageFactoryMock = $this->createPartialMock(PageFactory::class, ['create']);
        $this->ruleRepositoryMock = $this->getMockForAbstractClass(RuleRepositoryInterface::class);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->resultRedirectFactoryMock = $this->createPartialMock(RedirectFactory::class, ['create']);
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $context = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
                'messageManager' => $this->messageManagerMock
            ]
        );
        $this->controller = $objectManager->getObject(
            Edit::class,
            [
                'context' => $context,
                'ruleRepository' => $this->ruleRepositoryMock,
                'resultPageFactory' => $this->resultPageFactoryMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $rule = [
            'id' => 1,
            'name' => 'rule'
        ];
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($rule['id']);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($rule['id'])
            ->willReturn($ruleMock);

        $this->initResultPage();
        $ruleMock->expects($this->once())
            ->method('getName')
            ->willReturn($rule['name']);
        $this->titleMock->expects($this->once())
            ->method('prepend')
            ->with(__('Edit "%1" rule', $rule['name']));

        $this->assertSame($this->resultPageMock, $this->controller->execute());
    }

    /**
     * Test execute method on exception
     */
    public function testExecuteOnException()
    {
        $ruleId = 1;
        $exception = new NoSuchEntityException(__('exception'));

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($ruleId);
        $this->ruleRepositoryMock->expects($this->once())
            ->method('get')
            ->with($ruleId)
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage')
            ->with($exception, __('This rule no longer exists.'))
            ->willReturnSelf();

        $redirectMock = $this->createPartialMock(Redirect::class, ['setPath']);
        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($redirectMock);

        $redirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($redirectMock, $this->controller->execute());
    }

    /**
     * Test execute method on rule id is empty
     */
    public function testExecuteOnRuleIdIsEmpty()
    {
        $ruleId = null;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($ruleId);

        $this->initResultPage();
        $this->titleMock->expects($this->once())
            ->method('prepend')
            ->with(__('New Rule'));

        $this->assertSame($this->resultPageMock, $this->controller->execute());
    }

    /**
     * Init result page
     *
     * @return void
     */
    private function initResultPage()
    {
        $this->resultPageMock = $this->createPartialMock(Page::class, ['setActiveMenu', 'getConfig']);
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultPageMock);
        $this->resultPageMock->expects($this->once())
            ->method('setActiveMenu')
            ->with('Aheadworks_OnSale::rules')
            ->willReturnSelf();

        $configMock = $this->createPartialMock(Config::class, ['getTitle']);
        $this->resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($configMock);

        $this->titleMock = $this->createPartialMock(Title::class, ['prepend']);
        $configMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($this->titleMock);
    }
}
