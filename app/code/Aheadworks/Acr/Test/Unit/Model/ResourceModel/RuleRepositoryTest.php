<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\ResourceModel;

use Aheadworks\Acr\Model\ResourceModel\RuleRepository;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\RuleInterfaceFactory;
use Aheadworks\Acr\Api\Data\RuleSearchResultsInterface;
use Aheadworks\Acr\Api\Data\RuleSearchResultsInterfaceFactory;
use Aheadworks\Acr\Model\ResourceModel\Rule\Collection as RuleCollection;
use Aheadworks\Acr\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Acr\Model\Rule as RuleModel;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;

/**
 * Class RuleRepositoryTest
 * Test for \Aheadworks\Rma\Model\ResourceModel\RuleRepository
 *
 * @package Aheadworks\Acr\Test\Unit\Model\ResourceModel
 */
class RuleRepositoryTest extends TestCase
{
    /**
     * @var RuleRepository
     */
    private $model;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var RuleInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleFactoryMock;

    /**
     * @var RuleSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleSearchResultsFactoryMock;

    /**
     * @var RuleCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ruleCollectionFactoryMock;

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
        $this->ruleFactoryMock = $this->createPartialMock(RuleInterfaceFactory::class, ['create']);
        $this->ruleSearchResultsFactoryMock = $this->createPartialMock(
            RuleSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->ruleCollectionFactoryMock = $this->createPartialMock(
            RuleCollectionFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);

        $this->model = $objectManager->getObject(
            RuleRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'ruleFactory' => $this->ruleFactoryMock,
                'ruleSearchResultsFactory' => $this->ruleSearchResultsFactoryMock,
                'ruleCollectionFactory' => $this->ruleCollectionFactoryMock,
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
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($ruleMock);
        $ruleMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->data['id']);

        $ruleMock2 = $this->getMockForAbstractClass(RuleInterface::class);
        $this->ruleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleMock2);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($ruleMock2, $this->data['id']);
        $ruleMock2->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->assertSame($ruleMock2, $this->model->save($ruleMock));
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->ruleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($ruleMock, $this->data['id']);

        $this->assertSame($ruleMock, $this->model->get($this->data['id']));
    }

    /**
     * Testing of get method, that proper exception is thrown if cart history not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 1
     */
    public function testGetOnException()
    {
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $this->ruleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleMock);

        $this->assertSame($ruleMock, $this->model->get($this->data['id']));
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
        $searchResultsMock = $this->getMockForAbstractClass(RuleSearchResultsInterface::class, [], '', false);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $this->ruleSearchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $collectionMock = $this->createPartialMock(
            RuleCollection::class,
            ['addFieldToFilter', 'getSize', 'addOrder', 'setCurPage', 'setPageSize', 'getIterator']
        );
        $this->ruleCollectionFactoryMock
            ->method('create')
            ->willReturn($collectionMock);
        $ruleModelMock = $this->createPartialMock(RuleModel::class, ['getData']);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, RuleInterface::class);

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
            ->willReturn(new \ArrayIterator([$ruleModelMock]));

        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $this->ruleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleMock);
        $ruleModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->data);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($ruleMock, $this->data, RuleInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$ruleMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->ruleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($ruleMock, $this->data['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($ruleMock);

        $this->assertTrue($this->model->delete($ruleMock));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->data['id']);

        $this->ruleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($ruleMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($ruleMock, $this->data['id']);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($ruleMock);

        $this->assertTrue($this->model->deleteById($this->data['id']));
    }
}
