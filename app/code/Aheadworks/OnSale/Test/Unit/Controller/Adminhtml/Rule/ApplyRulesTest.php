<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Rule;

use Aheadworks\OnSale\Model\Rule\Job as RuleJob;
use Aheadworks\OnSale\Model\Rule\ReindexNotice;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Magento\Backend\App\Action\Context;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Controller\Adminhtml\Rule\ApplyRules;

/**
 * Class ApplyRulesTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Controller\Adminhtml\Rule
 */
class ApplyRulesTest extends TestCase
{
    /**
     * @var ApplyRules
     */
    private $controller;

    /**
     * @var RuleJob|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleJobMock;

    /**
     * @var ReindexNotice|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reindexNoticeMock;

    /**
     * @var ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultFactory;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

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
        $this->ruleJobMock = $this->createPartialMock(
            RuleJob::class,
            ['applyAll', 'hasSuccess', 'getSuccess', 'hasError', 'getError']
        );
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->resultFactory = $this->createPartialMock(ResultFactory::class, ['create']);
        $this->redirectMock = $this->createPartialMock(Redirect::class, ['setPath']);
        $this->resultFactory->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->redirectMock);

        $this->redirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();
        $context = $objectManager->getObject(
            Context::class,
            [
                'messageManager' => $this->messageManagerMock,
                'resultFactory' => $this->resultFactory,
            ]
        );

        $this->reindexNoticeMock = $this->createPartialMock(ReindexNotice::class, ['setDisabled']);
        $this->controller = $objectManager->getObject(
            ApplyRules::class,
            [
                'context' => $context,
                'ruleJob' => $this->ruleJobMock,
                'reindexNotice' => $this->reindexNoticeMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $success = true;
        $successMessage = __('Rules have been applied.');

        $this->ruleJobMock->expects($this->once())
            ->method('applyAll')
            ->willReturnSelf();
        $this->ruleJobMock->expects($this->once())
            ->method('hasSuccess')
            ->willReturn($success);
        $this->ruleJobMock->expects($this->once())
            ->method('getSuccess')
            ->willReturn($successMessage);
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with($successMessage);
        $this->reindexNoticeMock->expects($this->once())
            ->method('setDisabled')
            ->willReturnSelf();

        $this->assertSame($this->redirectMock, $this->controller->execute());
    }

    /**
     * Test execute method on error
     */
    public function testExecuteOnError()
    {
        $success = false;
        $error = true;
        $errorMessage = __('Some error message');

        $this->ruleJobMock->expects($this->once())
            ->method('applyAll')
            ->willReturnSelf();
        $this->ruleJobMock->expects($this->once())
            ->method('hasSuccess')
            ->willReturn($success);
        $this->ruleJobMock->expects($this->once())
            ->method('hasError')
            ->willReturn($error);
        $this->ruleJobMock->expects($this->once())
            ->method('getError')
            ->willReturn($errorMessage);
        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage');

        $this->assertSame($this->redirectMock, $this->controller->execute());
    }

    /**
     * Test execute method on exception
     */
    public function testExecuteOnException()
    {
        $success = true;
        $successMessage = __('Rules have been applied.');
        $exception = new \Exception('some exception');

        $this->ruleJobMock->expects($this->once())
            ->method('applyAll')
            ->willReturnSelf();
        $this->ruleJobMock->expects($this->once())
            ->method('hasSuccess')
            ->willReturn($success);
        $this->ruleJobMock->expects($this->once())
            ->method('getSuccess')
            ->willReturn($successMessage);
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with($successMessage);
        $this->reindexNoticeMock->expects($this->once())
            ->method('setDisabled')
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage')
            ->withConsecutive(
                [$exception],
                [__('Something went wrong while applying the rules')]
            );

        $this->assertSame($this->redirectMock, $this->controller->execute());
    }
}
