<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model;

use Magento\Framework\Phrase;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Acr\Model\CartRestoreManagement;
use Aheadworks\Acr\Api\Data\CartRestoreInterface;
use Aheadworks\Acr\Api\Data\CartRestoreInterfaceFactory;
use Aheadworks\Acr\Api\CartRestoreRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Acr\Api\Data\CartRestoreSearchResultsInterface;

/**
 * Class CartRestoreManagementTest
 * @package Aheadworks\Acr\Test\Unit\Model
 */
class CartRestoreManagementTest extends TestCase
{
    /**
     * @var CartRestoreInterfaceFactory
     */
    private $cartRestoreFactoryMock;

    /**
     * @var CartRestoreManagement
     */
    private $cartRestoreManagement;

    /**
     * @var CartRestoreRepositoryInterface
     */
    private $cartRestoreRepositoryMock;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->cartRestoreFactoryMock = $this->createMock(CartRestoreInterfaceFactory::class);
        $this->cartRestoreRepositoryMock = $this->createMock(CartRestoreRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);

        $this->cartRestoreManagement = $objectManager->getObject(
            CartRestoreManagement::class,
            [
                'cartRestoreFactory' => $this->cartRestoreFactoryMock,
                'cartRestoreRepository' => $this->cartRestoreRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
            ]
        );
    }

    /**
     * Test SaveRestoreCode method
     * @dataProvider getCartRestore
     */
    public function testSaveRestoreCode($cartRestore, $method)
    {
        $cartHistoryId = 1;
        $quoteId = 1;

        $this->cartRestoreRepositoryMock->expects($this->once())
            ->method('getByEventHistoryId')
            ->with($cartHistoryId)
            ->willReturn($this->cartRestoreFactoryMock);
        if ($cartRestore['cartRestore'] == true) {
            $this->cartRestoreFactoryMock->expects($this->any())
                ->method('create')
                ->willReturnSelf();
            $this->cartRestoreRepositoryMock->expects($this->any())
                ->method('save')
                ->with($this->cartRestoreFactoryMock);
        }
        $this->cartRestoreManagement->saveRestoreCode($cartHistoryId, $quoteId);
    }

    /**
     * Test SaveRestoreCodeOnExeption method
     * @dataProvider getCartRestore
     */
    public function testSaveRestoreCodeOnExeption($cartRestore, $method)
    {
        $cartHistoryId = 1;
        $quoteId = 1;

        $this->cartRestoreRepositoryMock->expects($this->once())
            ->method('getByEventHistoryId')
            ->with($cartHistoryId)
            ->willReturn($this->cartRestoreFactoryMock);
        if ($cartRestore == true) {
            if ($method == 'create') {
                $this->cartRestoreFactoryMock->expects($this->any())
                    ->method('create')
                    ->willReturnSelf()
                    ->willThrowException(new NoSuchEntityException());
            } else {
                $this->cartRestoreFactoryMock->expects($this->any())
                    ->method('create')
                    ->willReturnSelf();
            }
            if ($method == 'save') {
                $this->cartRestoreRepositoryMock->expects($this->any())
                    ->method('save')
                    ->with($this->cartRestoreFactoryMock)
                    ->willThrowException(new NoSuchEntityException());
            } else {
                $this->cartRestoreRepositoryMock->expects($this->any())
                    ->method('save')
                    ->with($this->cartRestoreFactoryMock);
            }
        }
        $this->cartRestoreManagement->saveRestoreCode($cartHistoryId, $quoteId);
    }

    /**
     * Test GetCartRestoreItemByHistoryId method
     */
    public function testGetCartRestoreItemByHistoryId()
    {
        $eventHistoryId = 1;
        $cartRestoreInterfaceMock = $this->createMock(CartRestoreInterface::class);

        $this->cartRestoreRepositoryMock->expects($this->once())
            ->method('getByEventHistoryId')
            ->with($eventHistoryId)
            ->willReturn($cartRestoreInterfaceMock);

        $this->assertSame(
            $cartRestoreInterfaceMock,
            $this->cartRestoreManagement->getCartRestoreItemByHistoryId($eventHistoryId)
        );
    }

    /**
     * @return array
     */
    public function getCartRestore()
    {
        return [
            [true, 'create'],
            [false, 'create'],
            [true, 'save'],
            [false, 'save']
        ];
    }
}
