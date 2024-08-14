<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Rule;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;
use Magento\Backend\App\Action\Context;
use Aheadworks\OnSale\Model\Rule\ReindexNotice;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Controller\Adminhtml\Rule\Index;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;

/**
 * Class IndexTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Rule
 */
class IndexTest extends TestCase
{
    /**
     * @var Index
     */
    private $controller;

    /**
     * @var PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageFactoryMock;

    /**
     * @var Page|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultPageMock;

    /**
     * @var ReindexNotice|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reindexNoticeMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $activeMenu = 'Aheadworks_OnSale::rules';
        $title = 'Rules';

        $this->resultPageFactoryMock = $this->createPartialMock(PageFactory::class, ['create']);
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $context = $objectManager->getObject(
            Context::class,
            [
                'messageManager' => $this->messageManagerMock
            ]
        );
        $this->resultPageMock = $this->createPartialMock(Page::class, ['setActiveMenu', 'getConfig']);
        $this->resultPageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultPageMock);
        $this->resultPageMock->expects($this->once())
            ->method('setActiveMenu')
            ->with($activeMenu)
            ->willReturnSelf();
        $configMock = $this->createPartialMock(Config::class, ['getTitle']);
        $this->resultPageMock->expects($this->once())
            ->method('getConfig')
            ->willReturn($configMock);
        $titleMock = $this->createPartialMock(Title::class, ['prepend']);
        $configMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($titleMock);
        $titleMock->expects($this->once())
            ->method('prepend')
            ->with(__($title));

        $this->reindexNoticeMock = $this->createPartialMock(ReindexNotice::class, ['isEnabled', 'getText']);
        $this->controller = $objectManager->getObject(
            Index::class,
            [
                'context' => $context,
                'resultPageFactory' => $this->resultPageFactoryMock,
                'reindexNotice' => $this->reindexNoticeMock
            ]
        );
    }

    /**
     * Test execute method in case reindex notice is enabled
     */
    public function testExecuteMethodWithEnabledIndexNotice()
    {
        $isReindexNoticeEnabled = true;
        $notice = 'test notice';

        $this->reindexNoticeMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn($isReindexNoticeEnabled);

        $this->reindexNoticeMock->expects($this->once())
            ->method('getText')
            ->willReturn($notice);

        $this->messageManagerMock->expects($this->once())
            ->method('addNoticeMessage')
            ->withConsecutive(__($notice));

        $this->assertSame($this->resultPageMock, $this->controller->execute());
    }

    /**
     * Test execute method in case reindex notice is disabled
     */
    public function testExecuteMethodWithoutEnabledIndexNotice()
    {
        $isReindexNoticeEnabled = false;

        $this->reindexNoticeMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn($isReindexNoticeEnabled);

        $this->assertSame($this->resultPageMock, $this->controller->execute());
    }
}
