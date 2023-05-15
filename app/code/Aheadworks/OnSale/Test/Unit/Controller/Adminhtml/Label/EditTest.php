<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Label;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Aheadworks\OnSale\Controller\Adminhtml\Label\Edit;
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
 * @package Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Label
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
     * @var LabelRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $labelRepositoryMock;

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
        $this->labelRepositoryMock = $this->getMockForAbstractClass(LabelRepositoryInterface::class);
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
                'labelRepository' => $this->labelRepositoryMock,
                'resultPageFactory' => $this->resultPageFactoryMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $label = [
            'id' => 1,
            'name' => 'label'
        ];
        $labelMock = $this->getMockForAbstractClass(LabelInterface::class);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($label['id']);
        $this->labelRepositoryMock->expects($this->once())
            ->method('get')
            ->with($label['id'])
            ->willReturn($labelMock);

        $this->initResultPage();
        $labelMock->expects($this->once())
            ->method('getName')
            ->willReturn($label['name']);
        $this->titleMock->expects($this->once())
            ->method('prepend')
            ->with(__('Edit "%1" label', $label['name']));

        $this->assertSame($this->resultPageMock, $this->controller->execute());
    }

    /**
     * Test execute method on exception
     */
    public function testExecuteOnException()
    {
        $labelId = 1;
        $exception = new NoSuchEntityException(__('exception'));

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($labelId);
        $this->labelRepositoryMock->expects($this->once())
            ->method('get')
            ->with($labelId)
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage')
            ->with($exception, __('This label no longer exists.'))
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
     * Test execute method on label id is empty
     */
    public function testExecuteOnLabelIdIsEmpty()
    {
        $labelId = null;
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id')
            ->willReturn($labelId);

        $this->initResultPage();
        $this->titleMock->expects($this->once())
            ->method('prepend')
            ->with(__('New Label'));

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
            ->with('Aheadworks_OnSale::labels')
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
