<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model;

use Aheadworks\OnSale\Api\Data\LabelSearchResultsInterface;
use Aheadworks\OnSale\Model\Label;
use Aheadworks\OnSale\Model\LabelRepository;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Api\Data\LabelInterfaceFactory;
use Aheadworks\OnSale\Api\Data\LabelSearchResultsInterfaceFactory;
use Aheadworks\OnSale\Model\ResourceModel\Label as LabelResourceModel;
use Aheadworks\OnSale\Model\ResourceModel\Label\CollectionFactory as LabelCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\OnSale\Model\ResourceModel\Label\Collection as LabelCollection;

/**
 * Class LabelRepository
 *
 * @package Aheadworks\OnSale\Test\Unit\Model
 */
class LabelRepositoryTest extends TestCase
{
    /**
     * @var LabelRepository
     */
    private $model;

    /**
     * @var LabelResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var LabelInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $labelInterfaceFactoryMock;

    /**
     * @var LabelCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $labelCollectionFactoryMock;

    /**
     * @var LabelSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var array
     */
    private $labelData = [
        'label_id' => 1,
        'name' => 'test label'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(LabelResourceModel::class, ['save', 'load', 'delete']);
        $this->labelInterfaceFactoryMock = $this->createPartialMock(LabelInterfaceFactory::class, ['create']);
        $this->labelCollectionFactoryMock = $this->createPartialMock(LabelCollectionFactory::class, ['create']);
        $this->searchResultsFactoryMock = $this->createPartialMock(
            LabelSearchResultsInterfaceFactory::class,
            ['create']
        );
        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->getMockForAbstractClass(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['populateWithArray']);
        $this->dataObjectProcessorMock = $this->createPartialMock(DataObjectProcessor::class, ['buildOutputDataArray']);
        $this->model = $objectManager->getObject(
            LabelRepository::class,
            [
                'resource' => $this->resourceMock,
                'labelInterfaceFactory' => $this->labelInterfaceFactoryMock,
                'labelCollectionFactory' => $this->labelCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var LabelInterface|\PHPUnit_Framework_MockObject_MockObject $labelMock */
        $labelMock = $this->createPartialMock(Label::class, ['getLabelId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $labelMock->expects($this->once())
            ->method('getLabelId')
            ->willReturn($this->labelData['label_id']);

        $this->assertSame($labelMock, $this->model->save($labelMock));
    }

    /**
     * Testing of save method on exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Exception message.
     */
    public function testSaveOnException()
    {
        $exception = new \Exception('Exception message.');

        /** @var LabelInterface|\PHPUnit_Framework_MockObject_MockObject $labelMock */
        $labelMock = $this->createPartialMock(Label::class, ['getLabelId']);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($labelMock);
    }

    /**
     * Testing of get method
     */
    public function testGet()
    {
        $labelId = 1;

        /** @var LabelInterface|\PHPUnit_Framework_MockObject_MockObject $labelMock */
        $labelMock = $this->createMock(Label::class);
        $this->labelInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($labelMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($labelMock, $labelId)
            ->willReturnSelf();
        $labelMock->expects($this->once())
            ->method('getLabelId')
            ->willReturn($labelId);

        $this->assertSame($labelMock, $this->model->get($labelId));
    }

    /**
     * Testing of get method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with label_id = 20
     */
    public function testGetOnException()
    {
        $labelId = 20;
        $labelMock = $this->createMock(Label::class);
        $this->labelInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($labelMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($labelMock, $labelId)
            ->willReturn(null);

        $this->model->get($labelId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $collectionSize = 1;
        /** @var LabelCollection|\PHPUnit_Framework_MockObject_MockObject $labelCollectionMock */
        $labelCollectionMock = $this->createPartialMock(
            LabelCollection::class,
            ['getSize', 'getItems']
        );
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $searchResultsMock = $this->getMockForAbstractClass(LabelSearchResultsInterface::class);
        /** @var Label|\PHPUnit_Framework_MockObject_MockObject $labelModelMock */
        $labelModelMock = $this->createPartialMock(Label::class, ['getData']);
        /** @var LabelInterface|\PHPUnit_Framework_MockObject_MockObject $labelMock */
        $labelMock = $this->getMockForAbstractClass(LabelInterface::class);

        $this->labelCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($labelCollectionMock);
        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($labelCollectionMock, LabelInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $labelCollectionMock);

        $labelCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);

        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $labelCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$labelModelMock]);

        $this->labelInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($labelMock);
        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($labelModelMock, LabelInterface::class)
            ->willReturn($this->labelData);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($labelMock, $this->labelData, LabelInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$labelMock])
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Testing of getList method
     */
    public function testDeleteById()
    {
        $labelId = '123';

        $labelMock = $this->createMock(Label::class);
        $labelMock->expects($this->any())
            ->method('getLabelId')
            ->willReturn($labelId);
        $this->labelInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($labelMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($labelMock, $labelId)
            ->willReturnSelf();
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($labelMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($labelId));
    }

    /**
     * Testing of delete method on exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteException()
    {
        $labelMock = $this->createMock(Label::class);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($labelMock)
            ->willThrowException(new \Exception());
        $this->model->delete($labelMock);
    }
}
