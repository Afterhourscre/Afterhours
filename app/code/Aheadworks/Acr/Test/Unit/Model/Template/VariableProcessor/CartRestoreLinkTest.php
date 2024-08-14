<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Template\VariableProcessor\CartRestoreLink;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\Data\CartRestoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Acr\Model\Email\UrlBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Aheadworks\Acr\Api\Data\CartHistorySearchResultsInterface;
use Aheadworks\Acr\Api\CartRestoreRepositoryInterface;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Api\CartRestoreManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Quote\Model\Quote;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class CartRestoreLinkTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class CartRestoreLinkTest extends TestCase
{
    /**
     * @var CartRestoreLink
     */
    private $cartRestoreLink;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerMock;

    /**
     * @var UrlBuilder
     */
    private $urlBuilderMock;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var CartRestoreRepositoryInterface
     */
    private $cartRestoreRepositoryMock;

    /**
     * @var CartHistoryRepositoryInterface
     */
    private $cartHistoryRepositoryMock;

    /**
     * @var CartRestoreManagementInterface
     */
    private $cartRestoreManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->urlBuilderMock = $this->createMock(UrlBuilder::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->cartRestoreRepositoryMock = $this->createMock(CartRestoreRepositoryInterface::class);
        $this->cartHistoryRepositoryMock = $this->createMock(CartHistoryRepositoryInterface::class);
        $this->cartRestoreManagementMock = $this->createMock(CartRestoreManagementInterface::class);

        $this->cartRestoreLink = $objectManager->getObject(
            CartRestoreLink::class,
            [
                'storeManager' => $this->storeManagerMock,
                'urlBuilder' => $this->urlBuilderMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'cartRestoreRepository' => $this->cartRestoreRepositoryMock,
                'cartHistoryRepository' => $this->cartHistoryRepositoryMock,
                'cartRestoreManagement' => $this->cartRestoreManagementMock,
            ]
        );
    }

    /**
     * Test Process method
     */
    public function testProcess()
    {
        $restoreCode = 'restore_code';
        $eventHistoryId = 1;
        $storeId = 1;
        $path = 'aw_acr/cart/restore';
        $scope = 'frontend';
        $url = 'http://123.com';

        $customer = $this->createMock(CustomerInterface::class);
        $store = $this->createMock(StoreManagerInterface::class);
        $quote = $this->createMock(Quote::class);
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $cartHistorySearchResultsInterface = $this->createMock(CartHistorySearchResultsInterface::class);
        $cartHistoryInterfaceMock = $this->createMock(CartHistoryInterface::class);
        $cartRestoreInterfaceMock = $this->createMock(CartRestoreInterface::class);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(CartHistoryInterface::REFERENCE_ID, $quote->getEntityId())
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->cartHistoryRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($cartHistorySearchResultsInterface);
        $cartHistorySearchResultsInterface->expects($this->once())
            ->method('getItems')
            ->willReturn([$cartHistoryInterfaceMock]);
        $cartHistoryInterfaceMock->expects($this->once())
            ->method('getId')
            ->willReturn($eventHistoryId);

        $this->cartRestoreManagementMock->expects($this->once())
            ->method('getCartRestoreItemByHistoryId')
            ->with($eventHistoryId)
            ->willReturn($cartRestoreInterfaceMock);
        $cartRestoreInterfaceMock->expects($this->once())
            ->method('getRestoreCode')
            ->willReturn($restoreCode);

        $quote->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customer);
        $customer->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($store);

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($path, $store, ['code' => $restoreCode, '_scope_to_url' => true], $scope)
            ->willReturn($url);

        $this->assertSame([Variables::CART_RESTORE_LINK => $url], $this->cartRestoreLink->process($quote, []));
    }

    /**
     * Test testProcess method
     */
    public function testProcessTest()
    {
        $path = 'aw_acr/cart/restore';
        $restoreCode = 'test';
        $storeId = 1;
        $url = 'http://123.com';
        $scope = 'frontend';

        $store = $this->createMock(StoreManagerInterface::class);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($store);

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with($path, $store, ['code' => $restoreCode, '_scope_to_url' => true], $scope)
            ->willReturn($url);
        $this->assertSame(
            [Variables::CART_RESTORE_LINK => $url],
            $this->cartRestoreLink->processTest(['store_id' => $storeId])
        );
    }
}
