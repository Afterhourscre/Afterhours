<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Controller\Adminhtml\Rule;

use Aheadworks\Coupongenerator\Controller\Adminhtml\Rule\NewAction;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Forward;
use Magento\Backend\Model\View\Result\ForwardFactory;

/**
 * Test for \Aheadworks\Coupongenerator\Controller\Adminhtml\Rule\NewAction
 */
class NewActionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var NewAction
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var ForwardFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $forwardFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->contextMock = $objectManager->getObject(
            Context::class,
            []
        );

        $this->forwardFactoryMock = $this->getMockBuilder(ForwardFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = $objectManager->getObject(
            NewAction::class,
            [
                'context' => $this->contextMock,
                'resultForwardFactory' => $this->forwardFactoryMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $resultForwardMock = $this->getMockBuilder(Forward::class)
            ->setMethods(['forward'])
            ->disableOriginalConstructor()
            ->getMock();
        $resultForwardMock->expects($this->once())
            ->method('forward')
            ->willReturnSelf();
        $this->forwardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultForwardMock);

        $this->assertSame($resultForwardMock, $this->controller->execute());
    }
}
