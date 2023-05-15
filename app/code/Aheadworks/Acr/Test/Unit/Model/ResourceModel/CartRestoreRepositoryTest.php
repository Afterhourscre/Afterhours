<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\ResourceModel;

use Aheadworks\Acr\Model\ResourceModel\CartRestoreRepository;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Acr\Api\Data\CartRestoreInterface;
use Aheadworks\Acr\Api\Data\CartRestoreInterfaceFactory;
use Aheadworks\Acr\Api\Data\CartRestoreSearchResultsInterface;
use Aheadworks\Acr\Api\Data\CartRestoreSearchResultsInterfaceFactory;
use Aheadworks\Acr\Model\ResourceModel\CartRestore\Collection as CartRestoreCollection;
use Aheadworks\Acr\Model\ResourceModel\CartRestore\CollectionFactory as CartRestoreCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Acr\Model\CartRestore as CartRestoreModel;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;

/**
 * Class CartRestoreRepositoryTest
 * Test for \Aheadworks\Rma\Model\ResourceModel\CartRestoreRepository
 *
 * @package Aheadworks\Acr\Test\Unit\Model\ResourceModel
 */
class CartRestoreRepositoryTest extends TestCase
{
    /**
     * @var CartRestoreRepository
     */
    private $model;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var CartRestoreInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartRestoreFactoryMock;

    /**
     * @var CartRestoreSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartRestoreSearchResultsFactoryMock;

    /**
     * @var CartRestoreCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cartRestoreCollectionFactoryMock;

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
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->cartRestoreFactoryMock = $this->createMock(CartRestoreInterfaceFactory::class);
        $this->cartRestoreSearchResultsFactoryMock = $this->createMock(CartRestoreSearchResultsInterfaceFactory::class);
        $this->cartRestoreCollectionFactoryMock = $this->createMock(CartRestoreCollectionFactory::class);
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);

        $this->model = $objectManager->getObject(
            CartRestoreRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'cartRestoreFactory' => $this->cartRestoreFactoryMock,
                'cartRestoreSearchResultsFactory' => $this->cartRestoreSearchResultsFactoryMock,
                'cartRestoreCollectionFactory' => $this->cartRestoreCollectionFactoryMock,
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
        $cartRestoreMock = $this->createMock(CartRestoreInterface::class);
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($cartRestoreMock);
        $cartRestoreMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->data['id']);

        $cartRestoreMock2 = $this->createMock(CartRestoreInterface::class);
        $this->cartRestoreFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartRestoreMock2);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($cartRestoreMock2, $this->data['id']);
        $cartRestoreMock2->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->assertSame($cartRestoreMock2, $this->model->save($cartRestoreMock));
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $cartRestoreMock = $this->createMock(CartRestoreInterface::class);
        $cartRestoreMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->cartRestoreFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartRestoreMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($cartRestoreMock, $this->data['id']);

        $this->assertSame($cartRestoreMock, $this->model->get($this->data['id']));
    }

    /**
     * Testing of get method, that proper exception is thrown if cart history not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 1
     */
    public function testGetOnException()
    {
        $cartRestoreMock = $this->createMock(CartRestoreInterface::class);
        $cartRestoreMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->cartRestoreFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartRestoreMock);

        $this->assertSame($cartRestoreMock, $this->model->get($this->data['id']));
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
        $searchResultsMock = $this->getMockForAbstractClass(CartRestoreSearchResultsInterface::class, [], '', false);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->cartRestoreSearchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collectionMock = $this->createMock(
            CartRestoreCollection::class,
            ['addFieldToFilter', 'getSize', 'addOrder', 'setCurPage', 'setPageSize', 'getIterator']
        );
        $this->cartRestoreCollectionFactoryMock
            ->method('create')
            ->willReturn($collectionMock);
        $cartRestoreModelMock = $this->createMock(CartRestoreModel::class, ['getData']);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, CartRestoreInterface::class);

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
            ->willReturn(new \ArrayIterator([$cartRestoreModelMock]));

        $cartRestoreMock = $this->getMockForAbstractClass(CartRestoreInterface::class);
        $this->cartRestoreFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartRestoreMock);
        $cartRestoreModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->data);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($cartRestoreMock, $this->data, CartRestoreInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$cartRestoreMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        $cartRestoreMock = $this->getMockForAbstractClass(CartRestoreInterface::class);
        $cartRestoreMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->cartRestoreFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartRestoreMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($cartRestoreMock, $this->data['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($cartRestoreMock);

        $this->assertTrue($this->model->delete($cartRestoreMock));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $cartRestoreMock = $this->getMockForAbstractClass(CartRestoreInterface::class);
        $cartRestoreMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->cartRestoreFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($cartRestoreMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($cartRestoreMock, $this->data['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($cartRestoreMock);

        $this->assertTrue($this->model->deleteById($this->data['id']));
    }
}
