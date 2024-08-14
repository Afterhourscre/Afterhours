<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\CartHistoryManagement;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterfaceFactory;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\QueueRepositoryInterface;
use Aheadworks\Acr\Api\QueueManagementInterface;
use Aheadworks\Acr\Api\RuleManagementInterface;
use Aheadworks\Acr\Api\Data\CartHistorySearchResultsInterface;
use Aheadworks\Acr\Api\Data\RuleSearchResultsInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\App\Emulation as AppEmulation;

/**
 * Class CartHistoryManagementTest
 * Test for \Aheadworks\Acr\Model\CartHistoryManagement
 *
 * @package Aheadworks\Acr\Test\Unit\Model
 */
class CartHistoryManagementTest extends TestCase
{
    /**
     * @var CartHistoryManagement
     */
    private $model;

    /**
     * @var CartHistoryInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartHistoryFactoryMock;

    /**
     * @var CartHistoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartHistoryRepositoryMock;

    /**
     * @var QueueRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueRepositoryMock;

    /**
     * @var QueueManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueManagementMock;

    /**
     * @var RuleManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleManagementMock;

    /**
     * @var CartRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTimeMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * @var AppEmulation|\PHPUnit_Framework_MockObject_MockObject
     */
    private $appEmulationMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->cartHistoryFactoryMock = $this->createPartialMock(CartHistoryInterfaceFactory::class, ['create']);
        $this->cartHistoryRepositoryMock = $this->getMockForAbstractClass(CartHistoryRepositoryInterface::class);
        $this->queueRepositoryMock = $this->getMockForAbstractClass(QueueRepositoryInterface::class);
        $this->queueManagementMock = $this->getMockForAbstractClass(QueueManagementInterface::class);
        $this->ruleManagementMock = $this->getMockForAbstractClass(RuleManagementInterface::class);
        $this->cartRepositoryMock = $this->getMockForAbstractClass(CartRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createPartialMock(
            SearchCriteriaBuilder::class,
            ['create', 'addFilter', 'setPageSize']
        );
        $this->dateTimeMock = $this->createPartialMock(DateTime::class, ['timestamp', 'date']);
        $this->loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);
        $this->appEmulationMock = $this->createPartialMock(
            AppEmulation::class,
            ['startEnvironmentEmulation', 'stopEnvironmentEmulation']
        );
        $this->model = $objectManager->getObject(
            CartHistoryManagement::class,
            [
                'cartHistoryFactory' => $this->cartHistoryFactoryMock,
                'cartHistoryRepository' => $this->cartHistoryRepositoryMock,
                'queueRepository' => $this->queueRepositoryMock,
                'queueManagement' => $this->queueManagementMock,
                'ruleManagement' => $this->ruleManagementMock,
                'cartRepository' => $this->cartRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'dateTime' => $this->dateTimeMock,
                'logger' => $this->loggerMock,
                'appEmulation' => $this->appEmulationMock
            ]
        );
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $expected = true;
        $cartIsActive = false;
        $cartHistoryMock = $this->createMock(CartHistoryInterface::class);
        $cartMock = $this->createMock(CartInterface::class);
        $this->initialProcess($cartHistoryMock, $cartMock);
        $cartMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($cartIsActive);

        $this->cartHistoryRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($cartHistoryMock);

        $this->assertEquals($expected, $this->model->process($cartHistoryMock));
    }

    /**
     * Test process method
     */
    public function testProcessCartNotActive()
    {
        $expected = true;
        $cartIsActive = true;
        $cartItemsCount = 2;
        $ruleTotalCount = 1;
        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $cartMock = $this->getMockForAbstractClass(CartInterface::class);
        $this->initialProcess($cartHistoryMock, $cartMock);

        $cartMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($cartIsActive);
        $cartMock->expects($this->once())
            ->method('getItemsCount')
            ->willReturn($cartItemsCount);

        $ruleSearchResultsMock = $this->getMockForAbstractClass(RuleSearchResultsInterface::class);
        $this->ruleManagementMock->expects($this->once())
            ->method('validate')
            ->with($cartHistoryMock)
            ->willReturn($ruleSearchResultsMock);
        $ruleSearchResultsMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn($ruleTotalCount);
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$ruleMock]);
        $this->queueManagementMock->expects($this->once())
            ->method('add')
            ->willReturn($ruleMock, $cartHistoryMock);
        $cartHistoryMock->expects($this->once())
            ->method('setProcessed')
            ->with(true);
        $this->cartHistoryRepositoryMock->expects($this->once())
            ->method('save')
            ->with($cartHistoryMock);

        $this->assertEquals($expected, $this->model->process($cartHistoryMock));
    }

    /**
     * Test processUnprocessedItems method
     */
    public function testProcessUnprocessedItems()
    {
        $expected = true;
        $cartHistoryTriggeredAt = 'date';
        $cartIsActive = false;
        $maxItemsCount = 1;

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(CartHistoryInterface::PROCESSED, false)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('setPageSize')
            ->with($maxItemsCount)
            ->willReturnSelf();
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $cartHistorySearchResultMock = $this->getMockForAbstractClass(CartHistorySearchResultsInterface::class);
        $this->cartHistoryRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($cartHistorySearchResultMock);

        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $cartHistorySearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$cartHistoryMock]);
        $cartHistoryMock->expects($this->once())
            ->method('getTriggeredAt')
            ->willReturn($cartHistoryTriggeredAt);

        $this->dateTimeMock->expects($this->exactly(2))
            ->method('timestamp')
            ->withConsecutive([$cartHistoryTriggeredAt], [])
            ->willReturn(1, 7200);

        $cartMock = $this->getMockForAbstractClass(CartInterface::class);
        $this->initialProcess($cartHistoryMock, $cartMock);
        $cartMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($cartIsActive);

        $this->assertEquals($expected, $this->model->processUnprocessedItems($maxItemsCount));
    }

    /**
     * Test processUnprocessedItems method, on exception
     */
    public function testProcessUnprocessedItemsOnException()
    {
        $cartHistoryTriggeredAt = 'date';
        $expected = true;
        $maxItemsCount = 1;

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(CartHistoryInterface::PROCESSED, false)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('setPageSize')
            ->with($maxItemsCount)
            ->willReturnSelf();
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $cartHistorySearchResultMock = $this->getMockForAbstractClass(CartHistorySearchResultsInterface::class);
        $this->cartHistoryRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($cartHistorySearchResultMock);

        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $cartHistorySearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$cartHistoryMock]);
        $cartHistoryMock->expects($this->once())
            ->method('getTriggeredAt')
            ->willReturn($cartHistoryTriggeredAt);

        $this->dateTimeMock->expects($this->once())
            ->method('timestamp')
            ->with($cartHistoryTriggeredAt)
            ->willThrowException(new \Exception('Exception'));
        $this->loggerMock->expects($this->once())
            ->method('error');

        $this->assertEquals($expected, $this->model->processUnprocessedItems($maxItemsCount));
    }

    /**
     * Test addCartToCartHistory method
     */
    public function testAddCartToCartHistory()
    {
        $expected = true;
        $cartHistoryProcessed = true;
        $cartHistoryId = 1;
        $currentTime = 'current time';
        $cartData = [
            'entity_id' => 1,
            'email' => 'email@example.com',
            'store_id' => 1,
            'customer_group_id' => 1,
            'customer_name' => 'customer name'
        ];

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(CartHistoryInterface::REFERENCE_ID, $cartData['entity_id'])
            ->willReturnSelf();
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $cartHistorySearchResultMock = $this->getMockForAbstractClass(CartHistorySearchResultsInterface::class);
        $this->cartHistoryRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($cartHistorySearchResultMock);

        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $cartHistorySearchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$cartHistoryMock]);

        $cartHistoryMock->expects($this->once())
            ->method('getProcessed')
            ->willReturn($cartHistoryProcessed);
        $cartHistoryMock->expects($this->once())
            ->method('getId')
            ->willReturn($cartHistoryId);
        $this->queueRepositoryMock->expects($this->once())
            ->method('deleteByCartHistoryId')
            ->with($cartHistoryId);
        $this->cartHistoryRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($cartHistoryMock);

        $cartHistoryMock2 = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $this->cartHistoryFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartHistoryMock2);

        $cartHistoryMock2->expects($this->once())
            ->method('setReferenceId')
            ->with($cartData['entity_id'])
            ->willReturnSelf();
        $cartHistoryMock2->expects($this->once())
            ->method('setCartData')
            ->willReturnSelf();
        $this->dateTimeMock->expects($this->once())
            ->method('date')
            ->willReturn($currentTime);
        $cartHistoryMock2->expects($this->once())
            ->method('setTriggeredAt')
            ->with($currentTime)
            ->willReturnSelf();

        $this->cartHistoryRepositoryMock->expects($this->once())
            ->method('save')
            ->with($cartHistoryMock2);

        $this->assertEquals($expected, $this->model->addCartToCartHistory($cartData));
    }

    /**
     * Test addCartToCartHistory method, cart is not valid
     */
    public function testAddCartToCartHistoryNotValid()
    {
        $expected = false;
        $cartData = [];

        $this->assertEquals($expected, $this->model->addCartToCartHistory($cartData));
    }

    /**
     * Initial for process
     *
     * @param CartHistoryInterface|\PHPUnit_Framework_MockObject_MockObject $cartHistoryMock
     * @param CartInterface|\PHPUnit_Framework_MockObject_MockObject $cartMock
     */
    private function initialProcess($cartHistoryMock, $cartMock)
    {
        $storeId = 1;
        $cartHistoryData = [
            'cart_data' => '{"store_id":1}',
            'reference_id' => 1
        ];

        $cartHistoryMock->expects($this->once())
            ->method('getCartData')
            ->willReturn($cartHistoryData['cart_data']);
        $this->appEmulationMock->expects($this->once())
            ->method('startEnvironmentEmulation')
            ->with($storeId, 'frontend', true);
        $cartHistoryMock->expects($this->once())
            ->method('getReferenceId')
            ->willReturn($cartHistoryData['reference_id']);

        $this->cartRepositoryMock->expects($this->once())
            ->method('get')
            ->with($cartHistoryData['reference_id'])
            ->willReturn($cartMock);
        $this->appEmulationMock->expects($this->once())
            ->method('stopEnvironmentEmulation');
    }
}
