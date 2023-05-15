<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Cookie
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\Cookie\Model;

use Extait\Cookie\Api\Data\CategoryInterface;
use Extait\Cookie\Api\Data\CategoryRepositoryInterface;
use Extait\Cookie\Api\Data\CategorySearchResultsInterfaceFactory;
use Extait\Cookie\Api\Data\CookieRepositoryInterface;
use Extait\Cookie\Helper\Repository as RepositoryHelper;
use Extait\Cookie\Model\ResourceModel\Category as CategoryResourceModel;
use Extait\Cookie\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var \Extait\Cookie\Helper\Repository
     */
    protected $repositoryHelper;

    /**
     * @var \Extait\Cookie\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Extait\Cookie\Model\ResourceModel\Category
     */
    protected $categoryResourceModel;

    /**
     * @var \Extait\Cookie\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Extait\Cookie\Model\CookieRepository
     */
    protected $cookieRepository;

    /**
     * @var \Extait\Cookie\Api\Data\CategorySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * CategoryRepository constructor.
     *
     * @param \Extait\Cookie\Helper\Repository $repositoryHelper
     * @param \Extait\Cookie\Model\CategoryFactory $categoryFactory
     * @param \Extait\Cookie\Model\ResourceModel\Category $categoryResourceModel
     * @param \Extait\Cookie\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Extait\Cookie\Api\Data\CookieRepositoryInterface $cookieRepository
     * @param \Extait\Cookie\Api\Data\CategorySearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        RepositoryHelper $repositoryHelper,
        CategoryFactory $categoryFactory,
        CategoryResourceModel $categoryResourceModel,
        CategoryCollectionFactory $categoryCollectionFactory,
        CookieRepositoryInterface $cookieRepository,
        CategorySearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->repositoryHelper = $repositoryHelper;
        $this->categoryFactory = $categoryFactory;
        $this->categoryResourceModel = $categoryResourceModel;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->cookieRepository = $cookieRepository;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     */
    public function get($id, $storeID = null)
    {
        if (!isset($this->instances[$id])) {
            $category = $this->categoryFactory->create();
            $category->setData('store_id', $storeID);

            $this->categoryResourceModel->load($category, $id);

            if ($category->getId() === null) {
                throw new NoSuchEntityException(__('Category with ID "%1" does not exist.', $id));
            }

            $this->fillCategoryWithCookies($category, $storeID);
            $this->cacheInstance($category);
        }

        return $this->instances[$id];
    }

    /**
     * Save the category entity.
     *
     * @param \Extait\Cookie\Api\Data\CategoryInterface|\Magento\Framework\Model\AbstractModel $category
     * @return \Extait\Cookie\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function save(CategoryInterface $category)
    {
        $this->categoryResourceModel->save($category);

        unset($this->instances[$category->getId()]);

        return $this->get($category->getId());
    }

    /**
     * Delete the cookie category entity.
     *
     * @param \Extait\Cookie\Api\Data\CategoryInterface|\Magento\Framework\Model\AbstractModel $category
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CategoryInterface $category)
    {
        try {
            unset($this->instances[$category->getId()]);
            $this->categoryResourceModel->delete($category);
        } catch (\Exception $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeID = null)
    {
        $collection = $this->categoryCollectionFactory->create();
        $searchResult = $this->searchResultsFactory->create();

        $this->repositoryHelper->addFiltersToCollection($searchCriteria, $collection);
        $this->repositoryHelper->addSortOrdersToCollection($searchCriteria, $collection);
        $this->repositoryHelper->addPagingToCollection($searchCriteria, $collection);
        $this->repositoryHelper->addStoreIDtoCollection($storeID, $collection);

        $collection->load();
        $this->fillCategoriesWithCookies($collection, $storeID);
        $this->cacheInstances($collection);

        return $this->repositoryHelper->buildSearchResult($searchResult, $searchCriteria, $collection);
    }

    /**
     * Fill categories related cookies.
     *
     * @param \Extait\Cookie\Model\ResourceModel\Category\Collection $collection
     * @param null|int $storeID
     */
    protected function fillCategoriesWithCookies(CategoryResourceModel\Collection $collection, $storeID = null)
    {
        /** @var \Extait\Cookie\Api\Data\CategoryInterface $category */
        foreach ($collection->getItems() as $category) {
            $this->fillCategoryWithCookies($category, $storeID);
        }
    }

    /**
     * Add to the category entity related cookies.
     *
     * @param \Extait\Cookie\Api\Data\CategoryInterface|\Magento\Framework\Model\AbstractModel $category
     * @param null|int $storeID
     */
    protected function fillCategoryWithCookies(CategoryInterface $category, $storeID = null)
    {
        $category->setData('cookies', $this->cookieRepository->getListByCategory($category, $storeID)->getItems());
    }

    /**
     * Cache instances.
     *
     * @param \Extait\Cookie\Model\ResourceModel\Category\Collection $collection
     */
    private function cacheInstances(CategoryResourceModel\Collection $collection)
    {
        /** @var \Extait\Cookie\Api\Data\CategoryInterface $category */
        foreach ($collection->getItems() as $category) {
            $this->cacheInstance($category);
        }
    }

    /**
     * Cache instance.
     *
     * @param \Extait\Cookie\Api\Data\CategoryInterface $category
     */
    private function cacheInstance(CategoryInterface $category)
    {
        $this->instances[$category->getId()] = $category;
    }
}
