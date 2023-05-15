<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\ResourceModel;

use Aheadworks\Acr\Model\ResourceModel\QueueRepository;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Api\Data\QueueInterfaceFactory;
use Aheadworks\Acr\Api\Data\QueueSearchResultsInterface;
use Aheadworks\Acr\Api\Data\QueueSearchResultsInterfaceFactory;
use Aheadworks\Acr\Model\ResourceModel\Queue\Collection as QueueCollection;
use Aheadworks\Acr\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Acr\Model\Queue as QueueModel;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Model\ResourceModel\Queue as QueueResource;

/**
 * Class QueueRepositoryTest
 * Test for \Aheadworks\Rma\Model\ResourceModel\QueueRepository
 *
 * @package Aheadworks\Acr\Test\Unit\Model\ResourceModel
 */
class QueueRepositoryTest extends TestCase
{
    /**
     * @var QueueRepository
     */
    private $model;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var QueueInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueFactoryMock;

    /**
     * @var QueueResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueResourceMock;

    /**
     * @var QueueSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueSearchResultsFactoryMock;

    /**
     * @var QueueCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueCollectionFactoryMock;

    /**
     * @var CartHistoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartHistoryRepositoryMock;

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
        'id' => 1,
        'cart_history_id' => 2
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
        $this->queueFactoryMock = $this->createPartialMock(QueueInterfaceFactory::class, ['create']);
        $this->queueResourceMock = $this->createPartialMock(QueueResource::class, ['deleteItemsByCartHistory']);
        $this->queueSearchResultsFactoryMock = $this->createPartialMock(
            QueueSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->queueCollectionFactoryMock = $this->createPartialMock(
            QueueCollectionFactory::class,
            ['create']
        );
        $this->cartHistoryRepositoryMock = $this->getMockForAbstractClass(CartHistoryRepositoryInterface::class);
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);

        $this->model = $objectManager->getObject(
            QueueRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'queueFactory' => $this->queueFactoryMock,
                'queueResource' => $this->queueResourceMock,
                'queueSearchResultsFactory' => $this->queueSearchResultsFactoryMock,
                'queueCollectionFactory' => $this->queueCollectionFactoryMock,
                'cartHistoryRepository' => $this->cartHistoryRepositoryMock,
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
        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($queueMock);
        $queueMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->data['id']);

        $queueMock2 = $this->getMockForAbstractClass(QueueInterface::class);
        $this->queueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueMock2);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($queueMock2, $this->data['id']);
        $queueMock2->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->assertSame($queueMock2, $this->model->save($queueMock));
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $queueMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->queueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($queueMock, $this->data['id']);

        $this->assertSame($queueMock, $this->model->get($this->data['id']));
    }

    /**
     * Testing of get method, that proper exception is thrown if cart history not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 1
     */
    public function testGetOnException()
    {
        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $queueMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->queueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueMock);

        $this->assertSame($queueMock, $this->model->get($this->data['id']));
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
        $searchResultsMock = $this->getMockForAbstractClass(QueueSearchResultsInterface::class, [], '', false);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->queueSearchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collectionMock = $this->createPartialMock(
            QueueCollection::class,
            ['addFieldToFilter', 'getSize', 'addOrder', 'setCurPage', 'setPageSize', 'getIterator']
        );
        $this->queueCollectionFactoryMock
            ->method('create')
            ->willReturn($collectionMock);
        $queueModelMock = $this->createPartialMock(QueueModel::class, ['getData']);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, QueueInterface::class);

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
            ->willReturn(new \ArrayIterator([$queueModelMock]));

        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $this->queueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueMock);
        $queueModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->data);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($queueMock, $this->data, QueueInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$queueMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $queueMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->queueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($queueMock, $this->data['id']);

        $queueMock->expects($this->exactly(3))
            ->method('getCartHistoryId')
            ->willReturn($this->data['cart_history_id']);

        $queueCollectionMock = $this->createPartialMock(QueueCollection::class, ['addFilter', 'getSize']);
        $this->queueCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueCollectionMock);
        $queueCollectionMock->expects($this->once())
            ->method('addFilter')
            ->with(QueueInterface::CART_HISTORY_ID, $this->data['cart_history_id']);
        $queueCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn(1);

        $this->cartHistoryRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($this->data['cart_history_id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($queueMock);

        $this->assertTrue($this->model->delete($queueMock));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $queueMock = $this->getMockForAbstractClass(QueueInterface::class);
        $queueMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->queueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($queueMock, $this->data['id']);
        $queueMock->expects($this->exactly(3))
            ->method('getCartHistoryId')
            ->willReturn($this->data['cart_history_id']);

        $queueCollectionMock = $this->createPartialMock(QueueCollection::class, ['addFilter', 'getSize']);
        $this->queueCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueCollectionMock);
        $queueCollectionMock->expects($this->once())
            ->method('addFilter')
            ->with(QueueInterface::CART_HISTORY_ID, $this->data['cart_history_id']);
        $queueCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn(1);

        $this->cartHistoryRepositoryMock->expects($this->once())
            ->method('deleteById')
            ->with($this->data['cart_history_id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($queueMock);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($queueMock);

        $this->assertTrue($this->model->deleteById($this->data['id']));
    }
}
