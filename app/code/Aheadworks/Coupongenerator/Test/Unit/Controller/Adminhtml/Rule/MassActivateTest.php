<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Test\Unit\Controller\Adminhtml\Rule;

use Aheadworks\Coupongenerator\Controller\Adminhtml\Rule\MassActivate;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Message\ManagerInterface;
use Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule\CollectionFactory;
use Aheadworks\Coupongenerator\Model\ResourceModel\Salesrule\Collection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Aheadworks\Coupongenerator\Model\Salesrule;
use Aheadworks\Coupongenerator\Model\Source\Rule\Status;

/**
 * Test for \Aheadworks\Coupongenerator\Controller\Adminhtml\Rule\MassActivate
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MassActivateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MassActivate
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
        $this->magentoSalesRuleRepositoryMock = $this->getMockForAbstractClass(RuleRepositoryInterface::class);

        $this->controller = $objectManager->getObject(
            MassActivate::class,
            [
                'context' => $this->contextMock,
                'collectionFactory' => $this->collectionFactoryMock,
                'filter' => $this->filterMock,
                'magentoSalesRuleRepository' => $this->magentoSalesRuleRepositoryMock
            ]
        );
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $magentoRuleId = 2;
        $count = 1;

        $salesruleModelMock = $this->getMockBuilder(Salesrule::class)
            ->setMethods(['getRuleId'])
            ->disableOriginalConstructor()
            ->getMock();
        $salesruleModelMock->expects($this->once())
            ->method('getRuleId')
            ->willReturn($magentoRuleId);

        $collectionMock = $this->getMockBuilder(Collection::class)
            ->setMethods(['getItems'])
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$salesruleModelMock]);
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($collectionMock)
            ->willReturn($collectionMock);

        $magentoRuleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $magentoRuleMock->expects($this->once())
            ->method('getRuleId')
            ->willReturn($magentoRuleId);
        $magentoRuleMock->expects($this->once())
            ->method('setIsActive')
            ->with(Status::STATUS_ACTIVE)
            ->willReturn(true);
        $this->magentoSalesRuleRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($magentoRuleId)
            ->willReturn($magentoRuleMock);
        $this->magentoSalesRuleRepositoryMock->expects($this->once())
            ->method('save')
            ->with($magentoRuleMock)
            ->willReturn($magentoRuleMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('A total of %1 rule(s) have been activated', $count))
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
