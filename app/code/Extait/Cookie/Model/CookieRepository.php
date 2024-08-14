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
use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Api\Data\CookieRepositoryInterface;
use Extait\Cookie\Api\Data\CookieSearchResultsInterfaceFactory;
use Extait\Cookie\Helper\Repository as RepositoryHelper;
use Extait\Cookie\Model\ResourceModel\Cookie as CookieResourceModel;
use Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory as CookieCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class CookieRepository implements CookieRepositoryInterface
{
    /**
     * @var \Extait\Cookie\Api\Data\CookieInterface[]
     */
    protected $instances = [];

    /**
     * @var \Extait\Cookie\Helper\Repository
     */
    protected $repositoryHelper;

    /**
     * @var \Extait\Cookie\Model\CookieFactory
     */
    protected $cookieFactory;

    /**
     * @var \Extait\Cookie\Model\ResourceModel\Cookie
     */
    protected $cookieResourceModel;

    /**
     * @var \Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory
     */
    protected $cookieCollectionFactory;

    /**
     * @var \Extait\Cookie\Model\CookieSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * CookieRepository constructor.
     *
     * @param \Extait\Cookie\Helper\Repository $repositoryHelper
     * @param \Extait\Cookie\Model\CookieFactory $cookieFactory
     * @param \Extait\Cookie\Model\ResourceModel\Cookie $cookieResourceModel
     * @param \Extait\Cookie\Model\ResourceModel\Cookie\CollectionFactory $cookieCollectionFactory
     * @param \Extait\Cookie\Api\Data\CookieSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        RepositoryHelper $repositoryHelper,
        CookieFactory $cookieFactory,
        CookieResourceModel $cookieResourceModel,
        CookieCollectionFactory $cookieCollectionFactory,
        CookieSearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->repositoryHelper = $repositoryHelper;
        $this->cookieFactory = $cookieFactory;
        $this->cookieResourceModel = $cookieResourceModel;
        $this->cookieCollectionFactory = $cookieCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function get($id, $storeID = null)
    {
        if (!isset($this->instances[$id])) {
            $cookie = $this->cookieFactory->create();
            $this->cookieResourceModel->load($cookie, $id);

            if ($cookie->getId() === null) {
                throw new NoSuchEntityException(__('Cookie with ID "%1" does not exist.', $id));
            }

            $this->cacheInstance($cookie);
        }

        return $this->instances[$id];
    }

    /**
     * @inheritDoc
     */
    public function getByName($name)
    {
        $cookieID = $this->cookieResourceModel->getIdByName($name);

        if ($cookieID == false) {
            throw new NoSuchEntityException(__('Cookie with name "%1" does not exist.', $name));
        }

        return $this->get($cookieID);
    }

    /**
     * Save the cookie entity.
     *
     * @param \Extait\Cookie\Api\Data\CookieInterface|\Magento\Framework\Model\AbstractModel $cookie
     * @return \Extait\Cookie\Api\Data\CookieInterface
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function save(CookieInterface $cookie)
    {
        $this->cookieResourceModel->save($cookie);

        unset($this->instances[$cookie->getId()]);

        return $this->get($cookie->getId());
    }

    /**
     * Delete the cookie entity.
     *
     * @param \Extait\Cookie\Api\Data\CookieInterface|\Magento\Framework\Model\AbstractModel $cookie
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CookieInterface $cookie)
    {
        try {
            unset($this->instances[$cookie->getId()]);
            $this->cookieResourceModel->delete($cookie);
        } catch (\Exception $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        }
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeID = null)
    {
        $collection = $this->cookieCollectionFactory->create();
        $searchResult = $this->searchResultsFactory->create();

        $this->repositoryHelper->addFiltersToCollection($searchCriteria, $collection);
        $this->repositoryHelper->addSortOrdersToCollection($searchCriteria, $collection);
        $this->repositoryHelper->addPagingToCollection($searchCriteria, $collection);
        $this->repositoryHelper->addStoreIDtoCollection($storeID, $collection);

        $collection->load();
        $this->cacheInstances($collection);

        return $this->repositoryHelper->buildSearchResult($searchResult, $searchCriteria, $collection);
    }

    /**
     * Get list of cookies by the category.
     *
     * @param \Extait\Cookie\Api\Data\CategoryInterface $category
     * @param null|int $storeID
     * @return \Extait\Cookie\Api\Data\CookieSearchResultsInterface
     */
    public function getListByCategory(CategoryInterface $category, $storeID = null)
    {
        $search = $this->searchCriteriaBuilder->addFilter(CookieInterface::CATEGORY_ID, $category->getId())->create();

        return $this->getList($search, $storeID);
    }

    /**
     * Cache instances.
     *
     * @param \Extait\Cookie\Model\ResourceModel\Cookie\Collection $collection
     */
    private function cacheInstances(CookieResourceModel\Collection $collection)
    {
        /** @var \Extait\Cookie\Api\Data\CookieInterface $cookie */
        foreach ($collection->getItems() as $cookie) {
            $this->cacheInstance($cookie);
        }
    }

    /**
     * Cache instance.
     *
     * @param \Extait\Cookie\Api\Data\CookieInterface $cookie
     */
    private function cacheInstance(CookieInterface $cookie)
    {
        $this->instances[$cookie->getId()] = $cookie;
    }
}
