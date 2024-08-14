<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\ResourceModel;

use Aheadworks\Acr\Model\ResourceModel\CartHistoryRepository;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterfaceFactory;
use Aheadworks\Acr\Api\Data\CartHistorySearchResultsInterface;
use Aheadworks\Acr\Api\Data\CartHistorySearchResultsInterfaceFactory;
use Aheadworks\Acr\Model\ResourceModel\CartHistory\Collection as CartHistoryCollection;
use Aheadworks\Acr\Model\ResourceModel\CartHistory\CollectionFactory as CartHistoryCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Acr\Model\CartHistory as CartHistoryModel;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;

/**
 * Class CartHistoryRepositoryTest
 * Test for \Aheadworks\Rma\Model\ResourceModel\CartHistoryRepository
 *
 * @package Aheadworks\Acr\Test\Unit\Model\ResourceModel
 */
class CartHistoryRepositoryTest extends TestCase
{
    /**
     * @var CartHistoryRepository
     */
    private $model;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var CartHistoryInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartHistoryFactoryMock;

    /**
     * @var CartHistorySearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartHistorySearchResultsFactoryMock;

    /**
     * @var CartHistoryCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartHistoryCollectionFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var array
     */
    private $data = [
        'id' => 1
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->entityManagerMock = $this->createPartialMock(EntityManager::class, ['load', 'save', 'delete']);
        $this->cartHistoryFactoryMock = $this->createPartialMock(CartHistoryInterfaceFactory::class, ['create']);
        $this->cartHistorySearchResultsFactoryMock = $this->createPartialMock(
            CartHistorySearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->cartHistoryCollectionFactoryMock = $this->createPartialMock(
            CartHistoryCollectionFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);

        $this->model = $objectManager->getObject(
            CartHistoryRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'cartHistoryFactory' => $this->cartHistoryFactoryMock,
                'cartHistorySearchResultsFactory' => $this->cartHistorySearchResultsFactoryMock,
                'cartHistoryCollectionFactory' => $this->cartHistoryCollectionFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock
            ]
        );
    }

    /**
     * Test save method
     */
    public function testSave()
    {
        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($cartHistoryMock);
        $cartHistoryMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->data['id']);

        $cartHistoryMock2 = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $this->cartHistoryFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartHistoryMock2);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($cartHistoryMock2, $this->data['id']);
        $cartHistoryMock2->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->assertSame($cartHistoryMock2, $this->model->save($cartHistoryMock));
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $cartHistoryMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->cartHistoryFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartHistoryMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($cartHistoryMock, $this->data['id']);

        $this->assertSame($cartHistoryMock, $this->model->get($this->data['id']));
    }

    /**
     * Testing of get method, that proper exception is thrown if cart history not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 1
     */
    public function testGetOnException()
    {
        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $cartHistoryMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->cartHistoryFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartHistoryMock);

        $this->assertSame($cartHistoryMock, $this->model->get($this->data['id']));
    }

    /**
     * Testing of getList method
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetList()
    {
        $filterName = 'Name';
        $filterValue = 'Sample value';
        $collectionSize = 5;
        $scCurrPage = 1;
        $scPageSize = 3;

        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class, [], '', false);
        $searchResultsMock = $this->getMockForAbstractClass(CartHistorySearchResultsInterface::class, [], '', false);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->cartHistorySearchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collectionMock = $this->createPartialMock(
            CartHistoryCollection::class,
            ['addFieldToFilter', 'getSize', 'addOrder', 'setCurPage', 'setPageSize', 'getIterator']
        );
        $this->cartHistoryCollectionFactoryMock
            ->method('create')
            ->willReturn($collectionMock);
        $cartHistoryModelMock = $this->createPartialMock(CartHistoryModel::class, ['getData']);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, CartHistoryInterface::class);

        $filterGroupMock = $this->createMock(FilterGroup::class);
        $filterMock = $this->createMock(Filter::class);
        $searchCriteriaMock->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$filterGroupMock]);
        $filterGroupMock->expects($this->once())
            ->method('getFilters')
            ->willReturn([$filterMock]);
        $filterMock->expects($this->once())
            ->method('getConditionType')
            ->willReturn(false);
        $filterMock->expects($this->atLeastOnce())
            ->method('getField')
            ->willReturn($filterName);
        $filterMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturn($filterValue);
        $collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with([$filterName], [['eq' => $filterValue]]);
        $collectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $sortOrderMock = $this->createMock(SortOrder::class);
        $searchCriteriaMock->expects($this->atLeastOnce())
            ->method('getSortOrders')
            ->willReturn([$sortOrderMock]);
        $sortOrderMock->expects($this->once())
            ->method('getField')
            ->willReturn($filterName);
        $collectionMock->expects($this->once())
            ->method('addOrder')
            ->with($filterName, SortOrder::SORT_ASC);
        $sortOrderMock->expects($this->once())
            ->method('getDirection')
            ->willReturn(SortOrder::SORT_ASC);
        $searchCriteriaMock->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn($scCurrPage);
        $collectionMock->expects($this->once())
            ->method('setCurPage')
            ->with($scCurrPage)
            ->willReturnSelf();
        $searchCriteriaMock->expects($this->once())
            ->method('getPageSize')
            ->willReturn($scPageSize);
        $collectionMock->expects($this->once())
            ->method('setPageSize')
            ->with($scPageSize)
            ->willReturn($collectionMock);
        $collectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$cartHistoryModelMock]));

        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $this->cartHistoryFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartHistoryMock);
        $cartHistoryModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->data);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($cartHistoryMock, $this->data, CartHistoryInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$cartHistoryMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $cartHistoryMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->cartHistoryFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartHistoryMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($cartHistoryMock, $this->data['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($cartHistoryMock);

        $this->assertTrue($this->model->delete($cartHistoryMock));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $cartHistoryMock = $this->getMockForAbstractClass(CartHistoryInterface::class);
        $cartHistoryMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->cartHistoryFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartHistoryMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($cartHistoryMock, $this->data['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($cartHistoryMock);

        $this->assertTrue($this->model->deleteById($this->data['id']));
    }
}
