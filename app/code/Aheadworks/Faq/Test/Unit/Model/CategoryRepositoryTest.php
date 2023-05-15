<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Faq\Model\ResourceModel\Category\Collection as CategoryCollection;
use Aheadworks\Faq\Model\ResourceModel\Category as ResourceCategory;
use Aheadworks\Faq\Model\ResourceModel\Category\CollectionFactory;
use Aheadworks\Faq\Api\Data\CategorySearchResultsInterfaceFactory;
use Aheadworks\Faq\Api\Data\CategorySearchResultsInterface;
use Aheadworks\Faq\Api\Data\CategoryInterfaceFactory;
use Aheadworks\Faq\Model\CategoryRepository;
use Aheadworks\Faq\Model\Category;
use Aheadworks\Faq\Model\ImageUploader;
use PHPUnit\Framework\TestCase;

/**
 * Test for CategoryRepository
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategoryRepositoryTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $categorySearchResultsMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryMock;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryResourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryCollectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryCollectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $filterGroupMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $filterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $sortOrderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $imageUploaderMock;

    /**
     * Initialize repository
     */
    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->imageUploaderMock = $this->createMock(ImageUploader::class);
        $this->categoryFactoryMock = $this->createMock(CategoryInterfaceFactory::class);
        $this->categoryCollectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $this->filterGroupMock = $this->createMock(FilterGroup::class);
        $this->sortOrderMock = $this->createMock(SortOrder::class);
        $this->categorySearchResultsMock = $this->createMock(CategorySearchResultsInterface::class);
        $this->categoryCollectionMock = $this->createMock(CategoryCollection::class);
        $this->searchResultsFactoryMock = $this->createMock(CategorySearchResultsInterfaceFactory::class);
        $this->categoryResourceMock = $this->createMock(ResourceCategory::class);
        $this->categoryMock = $this->createMock(Category::class);
        $this->filterMock = $this->createMock(Filter::class);

        $this->categoryRepository = $this->objectManager->getObject(
            CategoryRepository::class,
            [
                'resource' => $this->categoryResourceMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'categoryFactory' => $this->categoryFactoryMock,
                'categoryCollectionFactory' => $this->categoryCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'imageUploader' => $this->imageUploaderMock
            ]
        );
    }

    /**
     * @covers CategoryRepository::getById
     */
    public function testGetCategoryById()
    {
        $categoryId = 3;

        $this->categoryFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->categoryMock);
        $this->categoryResourceMock
            ->expects($this->once())
            ->method('load')
            ->with($this->categoryMock, $categoryId)
            ->willReturn($this->categoryMock);
        $this->categoryMock
            ->expects($this->once())
            ->method('getCategoryId')
            ->willReturn($categoryId);

        $this->assertEquals($this->categoryMock, $this->categoryRepository->getById($categoryId));
    }

    /**
     * Test throwing Exception during execution of
     * CategoryRepository::getById method
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetCategoryByIdException()
    {
        $categoryId = 3;

        $this->categoryFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->categoryMock);
        $this->categoryResourceMock
            ->expects($this->once())
            ->method('load')
            ->with($this->categoryMock, $categoryId)
            ->willReturn($this->categoryMock);
        $this->categoryMock
            ->expects($this->any())
            ->method('getCategoryId')
            ->willReturn(false);

        $this->categoryRepository->getById($categoryId);
    }

    /**
     * @covers CategoryRepository::save
     */
    public function testSaveCategory()
    {
        $categoryId = 3;

        $this->categoryResourceMock
            ->expects($this->once())
            ->method('save')
            ->with($this->categoryMock)
            ->willReturn($this->categoryMock);
        $this->categoryMock
            ->expects($this->once())
            ->method('getCategoryId')
            ->willReturn($categoryId);

        $this->assertEquals($this->categoryMock, $this->categoryRepository->save($this->categoryMock));
    }

    /**
     * Test throwing Exception during execution of
     * CategoryRepository::save method
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveCategoryException()
    {
        $this->categoryResourceMock
            ->expects($this->once())
            ->method('save')
            ->with($this->categoryMock)
            ->willThrowException(new \Exception());

        $this->categoryRepository->save($this->categoryMock);
    }

    /**
     * Test CategoryRepository::save
     * if Category Icon field is setted
     *
     * @depends testSaveCategory
     */
    public function testSaveCategoryWithSetCategoryIcon()
    {
        $categoryIcon = 'category_icon.jpg';

        $this->categoryResourceMock
            ->expects($this->once())
            ->method('save')
            ->with($this->categoryMock)
            ->willReturn($this->categoryMock);
        $this->categoryMock
            ->expects($this->atLeastOnce())
            ->method('getCategoryIcon')
            ->willReturn($categoryIcon);
        $this->imageUploaderMock
            ->expects($this->once())
            ->method('moveFileFromTmp')->with($categoryIcon)
            ->willReturn($categoryIcon);

        $this->assertEquals($this->categoryMock, $this->categoryRepository->save($this->categoryMock));
    }

    /**
     * Test CategoryRepository::save
     * if Article List Icon field is setted
     *
     * @depends testSaveCategory
     */
    public function testSaveCategoryWithSetArticleListIcon()
    {
        $articleListIcon = 'article_list_icon.jpg';

        $this->categoryResourceMock
            ->expects($this->once())
            ->method('save')
            ->with($this->categoryMock)
            ->willReturn($this->categoryMock);
        $this->categoryMock
            ->expects($this->atLeastOnce())
            ->method('getArticleListIcon')
            ->willReturn($articleListIcon);
        $this->imageUploaderMock
            ->expects($this->once())
            ->method('moveFileFromTmp')->with($articleListIcon)
            ->willReturn($articleListIcon);

        $this->assertEquals($this->categoryMock, $this->categoryRepository->save($this->categoryMock));
    }

    /**
     * @covers CategoryRepository::delete
     */
    public function testDeleteCategory()
    {
        $this->categoryResourceMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->categoryMock)
            ->willReturnSelf();
        $this->assertTrue($this->categoryRepository->delete($this->categoryMock));
    }

    /**
     * Test throwing Exception during execution of
     * CategoryRepository::delete method
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteCategoryException()
    {
        $this->categoryResourceMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->categoryMock)
            ->willThrowException(new \Exception());

        $this->categoryRepository->delete($this->categoryMock);
    }

    /**
     * @covers CategoryRepository::getList
     */
    public function testGetListWithEmptyCollection()
    {
        $categoryArray = [];
        $this->categoryCollectionFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->categoryCollectionMock);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn(false);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getSortOrders')
            ->willReturn(false);
        $this->categoryCollectionMock
            ->expects($this->any())
            ->method('getSize')
            ->willReturn(sizeof($categoryArray));
        $this->searchResultsFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->categorySearchResultsMock);
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteriaMock)
            ->willReturnSelf();
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setItems')
            ->with([])
            ->willReturnSelf();
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setTotalCount')
            ->with(sizeof($categoryArray))
            ->willReturnSelf();
        $this->assertEquals(
            $this->categorySearchResultsMock,
            $this->categoryRepository->getList($this->searchCriteriaMock)
        );
    }

    /**
     * Test get list
     *
     * @covers  CategoryRepository::getList
     * @depends testGetListWithEmptyCollection
     */
    public function testGetList()
    {
        $categoryArray = ['category_id' => 1, 'store_ids' => 2];
        $this->categoryCollectionFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->categoryCollectionMock);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn(false);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getSortOrders')
            ->willReturn(false);
        $this->categoryCollectionMock
            ->expects($this->any())
            ->method('getSize')
            ->willReturn(sizeof($categoryArray));

        $this->categoryCollectionMock
            ->expects($this->at(1))
            ->method('fetchItem')
            ->willReturn($this->categoryMock);

        $this->categoryMock
            ->expects($this->once())
            ->method('getData')
            ->willReturn($categoryArray);

        $this->categoryCollectionMock
            ->expects($this->at(2))
            ->method('fetchItem')
            ->willReturn(null);

        $this->categoryFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($categoryArray);

        $this->dataObjectHelperMock
            ->expects($this->once())
            ->method('populateWithArray')
            ->with($categoryArray, $categoryArray, Category::class)
            ->willReturnSelf();

        $this->searchResultsFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->categorySearchResultsMock);
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteriaMock)
            ->willReturnSelf();
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setItems')
            ->with([$categoryArray])
            ->willReturnSelf();
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setTotalCount')
            ->with(sizeof($categoryArray))
            ->willReturnSelf();
        $this->assertEquals(
            $this->categorySearchResultsMock,
            $this->categoryRepository->getList($this->searchCriteriaMock)
        );
    }

    /**
     * Test CategoryRepository::getList
     * if sort orders are setted
     *
     * @depends testGetList
     */
    public function testGetListWithEmptyCollectionAndSetSortOrders()
    {
        $categoryArray = [];
        $this->categoryCollectionFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->categoryCollectionMock);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn(false);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getSortOrders')
            ->willReturn([$this->sortOrderMock]);
        $this->sortOrderMock
            ->expects($this->any())
            ->method('getField')
            ->willReturn('category_id');
        $this->sortOrderMock
            ->expects($this->any())
            ->method('getDirection')
            ->willReturn('asc');
        $this->categoryCollectionMock
            ->expects($this->any())
            ->method('getSize')
            ->willReturn(sizeof($categoryArray));
        $this->searchResultsFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->categorySearchResultsMock);
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteriaMock)
            ->willReturnSelf();
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setItems')
            ->with([])
            ->willReturnSelf();
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setTotalCount')
            ->with(sizeof($categoryArray))
            ->willReturnSelf();
        $this->assertEquals(
            $this->categorySearchResultsMock,
            $this->categoryRepository->getList($this->searchCriteriaMock)
        );
    }

    /**
     * Test CategoryRepository::getList
     * if filter groups are setted
     *
     * @depends testGetList
     * @depends testGetListWithEmptyCollection
     */
    public function testGetListWithSetFilterGroups()
    {
        $categoryArray = [];
        $this->categoryCollectionFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->categoryCollectionMock);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$this->filterGroupMock]);
        $this->filterGroupMock
            ->expects($this->any())
            ->method('getFilters')
            ->willReturn([$this->filterMock]);
        $this->filterMock
            ->expects($this->any())
            ->method('getField')
            ->willReturn('is_enable');
        $this->filterMock
            ->expects($this->any())
            ->method('getConditionType')
            ->willReturn('');
        $this->filterMock
            ->expects($this->any())
            ->method('getValue')
            ->willReturn(true);
        $this->categoryCollectionMock
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with(['is_enable'], [['eq' => true]])
            ->willReturnSelf();
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getSortOrders')
            ->willReturn(false);
        $this->categoryCollectionMock
            ->expects($this->any())
            ->method('getSize')
            ->willReturn(sizeof($categoryArray));
        $this->searchResultsFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->categorySearchResultsMock);
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteriaMock)
            ->willReturnSelf();
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setItems')
            ->with([])
            ->willReturnSelf();
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setTotalCount')
            ->with(sizeof($categoryArray))
            ->willReturnSelf();
        $this->assertEquals(
            $this->categorySearchResultsMock,
            $this->categoryRepository->getList($this->searchCriteriaMock)
        );
    }

    /**
     * Test CategoryRepository::getList
     * if store filter is setted
     *
     * @depends testGetList
     * @depends testGetListWithEmptyCollection
     */
    public function testGetListWithSetStoreIdFilter()
    {
        $categoryArray = [];
        $this->categoryCollectionFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->categoryCollectionMock);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$this->filterGroupMock]);
        $this->filterGroupMock
            ->expects($this->any())
            ->method('getFilters')
            ->willReturn([$this->filterMock]);
        $this->filterMock
            ->expects($this->any())
            ->method('getField')
            ->willReturn('store_ids');
        $this->filterMock
            ->expects($this->any())
            ->method('getValue')
            ->willReturn(1);
        $this->categoryCollectionMock
            ->expects($this->once())
            ->method('addStoreFilter')
            ->with(1)
            ->willReturnSelf();
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getSortOrders')
            ->willReturn(false);
        $this->categoryCollectionMock
            ->expects($this->any())
            ->method('getSize')
            ->willReturn(sizeof($categoryArray));
        $this->searchResultsFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->categorySearchResultsMock);
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteriaMock)
            ->willReturnSelf();
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setItems')
            ->with([])
            ->willReturnSelf();
        $this->categorySearchResultsMock
            ->expects($this->once())
            ->method('setTotalCount')
            ->with(sizeof($categoryArray))
            ->willReturnSelf();
        $this->assertEquals(
            $this->categorySearchResultsMock,
            $this->categoryRepository->getList($this->searchCriteriaMock)
        );
    }
}
