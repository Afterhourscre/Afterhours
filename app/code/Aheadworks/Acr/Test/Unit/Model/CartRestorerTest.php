<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Acr\Api\Data\CartRestoreInterface;
use Aheadworks\Acr\Api\CartRestoreRepositoryInterface;
use Aheadworks\Acr\Api\CartHistoryManagementInterface;
use Aheadworks\Acr\Model\Cart\Merger;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Acr\Model\CartRestorer;
use Magento\Framework\Api\SearchCriteria;
use Aheadworks\Acr\Api\Data\CartRestoreSearchResultsInterface;
use Magento\Quote\Model\Quote;

/**
 * Class CartRestorerTest
 * @package Aheadworks\Acr\Test\Unit\Model
 */
class CartRestorerTest extends TestCase
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var Merger
     */
    private $mergerMock;

    /**
     * @var CartRestoreRepositoryInterface
     */
    private $cartRestoreRepositoryMock;

    /**
     * @var CheckoutSession
     */
    private $checkoutSessionMock;

    /**
     * @var CartHistoryManagementInterface
     */
    private $cartHistoryManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->mergerMock = $this->createMock(Merger::class);
        $this->cartRestoreRepositoryMock = $this->createMock(CartRestoreRepositoryInterface::class);
        $this->checkoutSessionMock = $this->createMock(CheckoutSession::class);
        $this->cartHistoryManagementMock = $this->createMock(CartHistoryManagementInterface::class);
        $this->cartRestorer = $objectManager->getObject(
            CartRestorer::class,
            [
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'cartRestoreRepository' => $this->cartRestoreRepositoryMock,
                'checkoutSession' => $this->checkoutSessionMock,
                'merger' => $this->mergerMock,
                'cartHistoryManagement' => $this->cartHistoryManagementMock,
            ]
        );
    }

    /**
     * Test restore method
     */
    public function testRestore()
    {
        $restoreCode = 'restore_code';
        $eventHistoryId = 1;
        $cartId = 1;
        $result = true;
        $customerId = 1;

        $cartRestoreInterfaceMock = $this->createMock(CartRestoreInterface::class);
        $cartObject = $this->createMock(Quote::class);

        $this->cartRestoreRepositoryMock->expects($this->once())
            ->method('getByCode')
            ->with($restoreCode)
            ->willReturn($cartRestoreInterfaceMock);
        $cartRestoreInterfaceMock->expects($this->once())
            ->method('getEventHistoryId')
            ->willReturn($eventHistoryId);
        $this->cartHistoryManagementMock->expects($this->once())
            ->method('getCartIdByEventHistoryId')
            ->with($eventHistoryId)
            ->willReturn($cartId);
        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($cartObject);
        $this->mergerMock->expects($this->once())
            ->method('mergeCartById')
            ->with($cartId, $cartObject, $customerId);

        $this->assertSame($result, $this->cartRestorer->restore($restoreCode, $customerId));
    }

    /**
     * @throws \ReflectionException
     */
    public function testRestoreOnException()
    {
        $restoreCode = 'restore_code';
        $eventHistoryId = 1;
        $cartId = 1;
        $result = false;
        $customerId = 1;

        $cartRestoreInterfaceMock = $this->createMock(CartRestoreInterface::class);
        $cartObject = $this->createMock(Quote::class);

        $this->cartRestoreRepositoryMock->expects($this->once())
            ->method('getByCode')
            ->with($restoreCode)
            ->willReturn($cartRestoreInterfaceMock);
        $cartRestoreInterfaceMock->expects($this->once())
            ->method('getEventHistoryId')
            ->willReturn($eventHistoryId);
        $this->cartHistoryManagementMock->expects($this->once())
            ->method('getCartIdByEventHistoryId')
            ->with($eventHistoryId)
            ->willReturn($cartId);
        $this->checkoutSessionMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($cartObject);
        $this->mergerMock->expects($this->once())
            ->method('mergeCartById')
            ->with($cartId, $cartObject, $customerId)
            ->willThrowException(new NoSuchEntityException());

        $this->assertSame($result, $this->cartRestorer->restore($restoreCode, $customerId));
    }
}
