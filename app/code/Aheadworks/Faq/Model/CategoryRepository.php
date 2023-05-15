<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\Search\FilterGroup;
use Aheadworks\Faq\Api\Data;
use Aheadworks\Faq\Api\CategoryRepositoryInterface;
use Aheadworks\Faq\Model\ResourceModel\Category as ResourceCategory;
use Aheadworks\Faq\Api\Data\CategoryInterfaceFactory as CategoryFactory;
use Aheadworks\Faq\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Aheadworks\Faq\Api\Data\CategorySearchResultsInterface;
use Aheadworks\Faq\Model\ResourceModel\Category\Collection;
use Aheadworks\Faq\Api\Data\CategoryInterface;

/**
 * Class CategoryRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var ResourceCategory
     */
    private $resource;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var Data\CategorySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Image uploader
     *
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var CategoryInterface[]
     */
    private $instancesById = [];

    /**
     * @param ResourceCategory $resource
     * @param CategoryFactory $categoryFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param Data\CategorySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        ResourceCategory $resource,
        DataObjectHelper $dataObjectHelper,
        CategoryFactory $categoryFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        Data\CategorySearchResultsInterfaceFactory $searchResultsFactory,
        ImageUploader $imageUploader
    ) {
        $this->resource = $resource;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->imageUploader = $imageUploader;
    }

    /**
     * Save Category
     *
     * @param Category|CategoryInterface $category
     * @return CategoryInterface
     * @throws CouldNotSaveException
     */
    public function save(CategoryInterface $category)
    {
        try {
            $this->resource->save($category);
            $this->instancesById[$category->getCategoryId()] = $category;

            if ($category->getCategoryIcon()) {
                $category->setCategoryIcon($this->imageUploader->moveFileFromTmp($category->getCategoryIcon()));
            }

            if ($category->getArticleListIcon()) {
                $category->setArticleListIcon(
                    $this->imageUploader->moveFileFromTmp($category->getArticleListIcon())
                );
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the category: %1',
                $exception->getMessage()
            ));
        }
        return $category;
    }

    /**
     * Load Category data by given Category Id
     *
     * @param int $categoryId
     * @return CategoryInterface
     * @throws NoSuchEntityException
     */
    public function getById($categoryId)
    {
        if (!isset($this->instancesById[$categoryId])) {
            /** @var Category|CategoryInterface $category */
            $category = $this->categoryFactory->create();
            $this->resource->load($category, $categoryId);
            if (!$category->getCategoryId()) {
                throw new NoSuchEntityException(__('FAQ Category with id "%1" does not exist.', $categoryId));
            }
            $this->instancesById[$categoryId] = $category;
        }
        return $this->instancesById[$categoryId];
    }

    /**
     * Load Category data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param SearchCriteriaInterface $criteria
     * @return CategorySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var Collection $collection */
        $collection = $this->categoryCollectionFactory->create();
        
        /** @var FilterGroup $filterGroup */
        $filterGroups = $criteria->getFilterGroups();
        if ($filterGroups) {
            foreach ($filterGroups as $group) {
                $this->addFilterGroupToCollection($group, $collection);
            }
        }

        /** @var SortOrder $sortOrder */
        $sortOrders = $criteria->getSortOrders();

        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() === SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        }

        $items = [];
        if ($collection->getSize()) {
            while ($category = $collection->fetchItem()) {
                $items[] = $categoryData = $this->categoryFactory->create();
                $this->dataObjectHelper->populateWithArray($categoryData, $category->getData(), Category::class);
            }
        }

        /** @var CategorySearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($criteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * Delete Category
     *
     * @param Category|CategoryInterface $category
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(CategoryInterface $category)
    {
        try {
            $this->resource->delete($category);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the category: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete Category by given Category Id
     *
     * @param int $categoryId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($categoryId)
    {
        return $this->delete($this->getById($categoryId));
    }

    /**
     * Helper function that adds a FilterGroup to the collection
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    private function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            if ($filter->getField() == 'store_ids') {
                $collection->addStoreFilter($filter->getValue());
                continue;
            }
            $condition = $filter->getConditionType() ?: 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}
