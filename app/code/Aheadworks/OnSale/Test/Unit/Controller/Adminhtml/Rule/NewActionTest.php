<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Rule;

use Aheadworks\OnSale\Controller\Adminhtml\Rule\NewAction;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\App\Action\Context;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Class NewAction
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule
 */
class NewActionTest extends TestCase
{
    /**
     * @var NewAction
     */
    private $controller;

    /**
     * @var ForwardFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultForwardFactoryMock;

    /**
     * @var Forward|\PHPUnit_Framework_MockObject_MockObject
     */
    private $forwardMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $context = $objectManager->getObject(
            Context::class,
            []
        );

        $this->resultForwardFactoryMock = $this->createPartialMock(ForwardFactory::class, ['create']);
        $this->forwardMock = $this->createPartialMock(Forward::class, ['forward']);
        $this->resultForwardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->forwardMock);
        $this->forwardMock->expects($this->once())
            ->method('forward')
            ->with('edit')
            ->willReturnSelf();

        $this->controller = $objectManager->getObject(
            NewAction::class,
            [
                'context' => $context,
                'resultForwardFactory' => $this->resultForwardFactoryMock,
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $this->controller->execute();
    }
}
