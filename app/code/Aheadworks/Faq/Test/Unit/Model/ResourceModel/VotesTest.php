<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Test\Unit\Model\ResourceModel;

use Aheadworks\Faq\Model\ResourceModel\Votes;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VotesTest extends TestCase
{
    /**
     * Main table const
     */
    const MAIN_TABLE = 'aw_faq_article_votes';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $selectMock;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceConnectionMock;

    /**
     * @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionMock;

    /**
     * @var Votes
     */
    private $votesObject;

    /**
     * Initialize resource model
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->contextMock = $this->createMock(Context::class);
        $this->selectMock = $this->createMock(Select::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->connectionMock = $this->createMock(AdapterInterface::class);

        $this->contextMock
            ->expects($this->once())
            ->method('getResources')
            ->willReturn($this->resourceConnectionMock);

        $this->resourceConnectionMock
            ->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->connectionMock);

        $this->resourceConnectionMock
            ->expects($this->any())
            ->method('getTableName')
            ->willReturnArgument(0);

        $this->votesObject = $this->objectManager->getObject(
            Votes::class,
            ['context' => $this->contextMock]
        );
    }

    /**
     * Prepare mocks for test method isSetVisitorAction
     *
     * @param $visitorId
     * @param $articleId
     * @param $action
     * @param bool $expectedResult
     */
    private function prepareIsSetVisitorAction($visitorId, $articleId, $action, $expectedResult = true)
    {
        $this->connectionMock
            ->expects($this->once())
            ->method('select')
            ->willReturn($this->selectMock);

        $this->selectMock
            ->expects($this->once())
            ->method('from')
            ->with(['faq' => self::MAIN_TABLE])
            ->willReturnSelf();

        $this->selectMock
            ->expects($this->at(1))
            ->method('where')
            ->with('faq.article_id = ?', $articleId)
            ->willReturnSelf();

        $this->selectMock
            ->expects($this->at(2))
            ->method('where')
            ->with('faq.visitor_id = ?', $visitorId)
            ->willReturnSelf();

        $this->selectMock
            ->expects($this->at(3))
            ->method('where')
            ->with('faq.action = ?', $action)
            ->willReturnSelf();

        $this->connectionMock
            ->expects($this->once())
            ->method('fetchRow')
            ->with($this->selectMock)
            ->willReturn($expectedResult);
    }

    /**
     * Prepare mocks for test method isSetVisitorAction
     *
     * @param $customerId
     * @param $articleId
     * @param $action
     * @param bool $expectedResult
     * @internal param $visitorId
     */
    private function prepareIsSetCustomerAction($customerId, $articleId, $action, $expectedResult = true)
    {
        $this->connectionMock
            ->expects($this->once())
            ->method('select')
            ->willReturn($this->selectMock);

        $this->selectMock
            ->expects($this->once())
            ->method('from')
            ->with(['faq' => self::MAIN_TABLE])
            ->willReturnSelf();

        $this->selectMock
            ->expects($this->at(1))
            ->method('where')
            ->with('faq.article_id = ?', $articleId)
            ->willReturnSelf();

        $this->selectMock
            ->expects($this->at(2))
            ->method('where')
            ->with('faq.customer_id = ?', $customerId)
            ->willReturnSelf();

        $this->selectMock
            ->expects($this->at(3))
            ->method('where')
            ->with('faq.action = ?', $action)
            ->willReturnSelf();

        $this->connectionMock
            ->expects($this->once())
            ->method('fetchRow')
            ->with($this->selectMock)
            ->willReturn($expectedResult);
    }

    /**
     * Action for visitor exist in DB
     *
     * @covers Votes::isSetVisitorAction
     */
    public function testExistIsSetVisitorAction()
    {
        $visitorId = 1;
        $articleId = 2;
        $action = 'action';

        $this->prepareIsSetVisitorAction($visitorId, $articleId, $action);

        $this->assertTrue($this->votesObject->isSetVisitorAction($visitorId, $articleId, $action));
    }

    /**
     * Action for visitor not exist in DB
     *
     * @covers  Votes::isSetVisitorAction
     * @depends testExistIsSetVisitorAction
     */
    public function testNotExistIsSetVisitorAction()
    {
        $visitorId = 1;
        $articleId = 2;
        $action = 'action';

        $this->prepareIsSetVisitorAction($visitorId, $articleId, $action, false);

        $this->assertFalse($this->votesObject->isSetVisitorAction($visitorId, $articleId, $action));
    }

    /**
     * Action for customer exist in DB
     *
     * @covers Votes::isSetVisitorAction
     */
    public function testExistIsSetCustomerAction()
    {
        $visitorId = 1;
        $articleId = 2;
        $action = 'action';

        $this->prepareIsSetCustomerAction($visitorId, $articleId, $action);

        $this->assertTrue($this->votesObject->isSetCustomerAction($visitorId, $articleId, $action));
    }

    /**
     * Action for customer not exist in DB
     *
     * @covers  Votes::isSetVisitorAction
     * @depends testExistIsSetVisitorAction
     */
    public function testNotExistIsSetCustomerAction()
    {
        $visitorId = 1;
        $articleId = 2;
        $action = 'action';

        $this->prepareIsSetCustomerAction($visitorId, $articleId, $action, false);

        $this->assertFalse($this->votesObject->isSetCustomerAction($visitorId, $articleId, $action));
    }

    /**
     * Add visitor action
     * Visitor action not exist in database
     *
     * @covers  Votes::addVisitorAction()
     * @depends testExistIsSetVisitorAction
     */
    public function testAddVisitorAction()
    {
        $visitorId = 1;
        $articleId = 2;
        $action = 'action';

        $this->prepareIsSetVisitorAction($visitorId, $articleId, $action, false);

        $this->connectionMock
            ->expects($this->once())
            ->method('insert')
            ->with(self::MAIN_TABLE, ['article_id' => $articleId, 'visitor_id' => $visitorId, 'action' => $action])
            ->willReturn($visitorId);

        $this->assertInstanceOf(Votes::class, $this->votesObject->addVisitorAction($visitorId, $articleId, $action));
    }

    /**
     * Add visitor action
     * Visitor action exist in database
     *
     * @covers  Votes::addVisitorAction()
     * @depends testExistIsSetVisitorAction
     */
    public function testAddVisitorActionExist()
    {
        $visitorId = 1;
        $articleId = 2;
        $action = 'action';

        $this->prepareIsSetVisitorAction($visitorId, $articleId, $action, true);

        $this->connectionMock
            ->expects($this->never())
            ->method('insert');

        $this->assertInstanceOf(Votes::class, $this->votesObject->addVisitorAction($visitorId, $articleId, $action));
    }

    /**
     * Add customer action
     * Customer action not exist in database
     *
     * @covers  Votes::addCustomerAction()
     * @depends testExistIsSetCustomerAction
     */
    public function testAddCustomerAction()
    {
        $customerId = 1;
        $articleId = 2;
        $action = 'action';

        $this->prepareIsSetCustomerAction($customerId, $articleId, $action, false);

        $this->connectionMock
            ->expects($this->once())
            ->method('insert')
            ->with(self::MAIN_TABLE, ['article_id' => $articleId, 'customer_id' => $customerId, 'action' => $action])
            ->willReturn($customerId);

        $this->assertInstanceOf(Votes::class, $this->votesObject->addCustomerAction($customerId, $articleId, $action));
    }

    /**
     * Add customer action
     * Customer action exist in database
     *
     * @covers  Votes::addCustomerAction()
     * @depends testExistIsSetCustomerAction
     */
    public function testAddCustomerActionExist()
    {
        $customerId = 1;
        $articleId = 2;
        $action = 'action';

        $this->prepareIsSetCustomerAction($customerId, $articleId, $action, true);

        $this->connectionMock
            ->expects($this->never())
            ->method('insert');

        $this->assertInstanceOf(Votes::class, $this->votesObject->addCustomerAction($customerId, $articleId, $action));
    }

    /**
     * Remove visitor action
     *
     * @covers  Votes::removeVisitorAction
     */
    public function testRemoveVisitorAction()
    {
        $visitorId = 1;
        $articleId = 2;
        $action = 'action';

        $this->connectionMock
            ->expects($this->once())
            ->method('delete')
            ->with(
                self::MAIN_TABLE,
                ['article_id = ?' => $articleId, 'visitor_id = ?' => $visitorId, 'action = ?' => $action]
            )
            ->willReturn($visitorId);

        $this->assertInstanceOf(Votes::class, $this->votesObject->removeVisitorAction($visitorId, $articleId, $action));
    }

    /**
     * Remove customer action
     *
     * @covers  Votes::removeCustomerAction
     */
    public function testRemoveCustomerAction()
    {
        $customerId = 1;
        $articleId = 2;
        $action = 'action';

        $this->connectionMock
            ->expects($this->once())
            ->method('delete')
            ->with(
                self::MAIN_TABLE,
                ['article_id = ?' => $articleId, 'customer_id = ?' => $customerId, 'action = ?' => $action]
            )
            ->willReturn($customerId);

        $this->assertInstanceOf(
            Votes::class,
            $this->votesObject->removeCustomerAction($customerId, $articleId, $action)
        );
    }
}
