<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Test\Unit\Model\Helpfulness;

use Aheadworks\Faq\Model\Helpfulness\Manager;
use Aheadworks\Faq\Model\ResourceModel\Votes;
use Magento\Customer\Model\Visitor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 * Test for Manager
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ManagerTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var CurrentCustomer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $currentCustomer;

    /**
     * @var Votes|\PHPUnit_Framework_MockObject_MockObject
     */
    private $votesResourceMock;

    /**
     * @var Visitor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $visitorMock;

    /**
     * @var HttpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpContextMock;

    /**
     * @var Manager
     */
    private $managerObject;

    /**
     * Initialize Manager
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->currentCustomer = $this->createMock(CurrentCustomer::class);
        $this->visitorMock = $this->createMock(Visitor::class);
        $this->votesResourceMock = $this->createMock(Votes::class);
        $this->httpContextMock = $this->createMock(HttpContext::class);

        $this->managerObject = $this->objectManager->getObject(
            Manager::class,
            [
                'currentCustomer' => $this->currentCustomer,
                'visitor' => $this->visitorMock,
                'votesResource' => $this->votesResourceMock,
                'httpContext' => $this->httpContextMock
            ]
        );
    }

    /**
     * Add vote action when customer is logged in
     *
     * @covers Manager::addAction
     */
    public function testAddActionWhenCustomerIsLoggedIn()
    {
        $articleId = 1;
        $customerId = 3;
        $action = 'action';

        $this->currentCustomer
            ->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->httpContextMock->expects($this->once())
            ->method('getValue')
            ->with(CustomerContext::CONTEXT_AUTH)
            ->willReturn(true);

        $this->votesResourceMock
            ->expects($this->once())
            ->method('addCustomerAction')
            ->with($customerId, $articleId, $action);

        $this->visitorMock
            ->expects($this->never())
            ->method('getId');

        $this->votesResourceMock
            ->expects($this->never())
            ->method('addVisitorAction');

        $this->assertInstanceOf(Manager::class, $this->managerObject->addAction($action, $articleId));
    }

    /**
     * Add vote action when customer is not logged in
     *
     * @covers  Manager::addAction
     * @depends testAddActionWhenCustomerIsLoggedIn
     */
    public function testAddActionWhenCustomerIsNotLoggedIn()
    {
        $articleId = 1;
        $visitorId = 3;
        $action = 'action';

        $this->currentCustomer
            ->expects($this->never())
            ->method('getCustomerId');

        $this->votesResourceMock
            ->expects($this->never())
            ->method('addCustomerAction');

        $this->httpContextMock->expects($this->once())
            ->method('getValue')
            ->with(CustomerContext::CONTEXT_AUTH)
            ->willReturn(false);

        $this->visitorMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($visitorId);

        $this->votesResourceMock
            ->expects($this->once())
            ->method('addVisitorAction')
            ->with($visitorId, $articleId, $action);

        $this->assertInstanceOf(Manager::class, $this->managerObject->addAction($action, $articleId));
    }

    /**
     * Remove vote action when customer is logged in
     *
     * @covers Manager::removeAction
     */
    public function testRemoveActionWhenCustomerIsLoggedIn()
    {
        $articleId = 1;
        $customerId = 3;
        $action = 'action';

        $this->currentCustomer
            ->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->votesResourceMock
            ->expects($this->once())
            ->method('removeCustomerAction')
            ->with($customerId, $articleId, $action);

        $this->httpContextMock->expects($this->once())
            ->method('getValue')
            ->with(CustomerContext::CONTEXT_AUTH)
            ->willReturn(true);

        $this->visitorMock
            ->expects($this->never())
            ->method('getId');

        $this->votesResourceMock
            ->expects($this->never())
            ->method('removeVisitorAction');

        $this->assertInstanceOf(Manager::class, $this->managerObject->removeAction($action, $articleId));
    }

    /**
     * Add vote action when customer is not logged in
     *
     * @covers  Manager::removeAction
     * @depends testRemoveActionWhenCustomerIsLoggedIn
     */
    public function testRemoveActionWhenCustomerIsNotLoggedIn()
    {
        $articleId = 1;
        $visitorId = 3;
        $action = 'action';

        $this->currentCustomer
            ->expects($this->never())
            ->method('getCustomerId');

        $this->votesResourceMock
            ->expects($this->never())
            ->method('removeCustomerAction');

        $this->httpContextMock->expects($this->once())
            ->method('getValue')
            ->with(CustomerContext::CONTEXT_AUTH)
            ->willReturn(false);

        $this->visitorMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($visitorId);

        $this->votesResourceMock
            ->expects($this->once())
            ->method('removeVisitorAction')
            ->with($visitorId, $articleId, $action);

        $this->assertInstanceOf(Manager::class, $this->managerObject->removeAction($action, $articleId));
    }

    /**
     * Check vote status when customer is logged in
     *
     * @covers Manager::isSetAction
     */
    public function testIsSetActionWhenCustomerIsLoggedIn()
    {
        $articleId = 1;
        $customerId = 3;
        $action = 'action';

        $this->currentCustomer
            ->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->httpContextMock->expects($this->once())
            ->method('getValue')
            ->with(CustomerContext::CONTEXT_AUTH)
            ->willReturn(true);

        $this->votesResourceMock
            ->expects($this->once())
            ->method('isSetCustomerAction')
            ->with($customerId, $articleId, $action)
            ->willReturnSelf();

        $this->votesResourceMock
            ->expects($this->never())
            ->method('isSetVisitorAction');

        $this->visitorMock
            ->expects($this->never())
            ->method('getId');

        $this->assertEquals($this->votesResourceMock, $this->managerObject->isSetAction($action, $articleId));
        $this->assertEquals($this->votesResourceMock, $this->managerObject->isSetAction($action, $articleId));
    }

    /**
     * Check vote status when customer is not logged in
     *
     * @covers  Manager::isSetAction
     * @depends testIsSetActionWhenCustomerIsLoggedIn
     */
    public function testIsSetActionWhenCustomerIsNotLoggedIn()
    {
        $articleId = 1;
        $customerId = 3;
        $action = 'action';

        $this->visitorMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);

        $this->votesResourceMock
            ->expects($this->once())
            ->method('isSetVisitorAction')
            ->with($customerId, $articleId, $action)
            ->willReturnSelf();

        $this->votesResourceMock
            ->expects($this->never())
            ->method('isSetCustomerAction');

        $this->currentCustomer
            ->expects($this->never())
            ->method('getCustomerId');

        $this->assertEquals($this->votesResourceMock, $this->managerObject->isSetAction($action, $articleId));
        $this->assertEquals($this->votesResourceMock, $this->managerObject->isSetAction($action, $articleId));
    }
}
