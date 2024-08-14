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
use Aheadworks\Faq\Api\Data;
use Aheadworks\Faq\Api\ArticleRepositoryInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Aheadworks\Faq\Model\ResourceModel\Article as ResourceArticle;
use Aheadworks\Faq\Api\Data\ArticleInterfaceFactory as ArticleFactory;
use Aheadworks\Faq\Model\ResourceModel\Article\CollectionFactory as ArticleCollectionFactory;
use Aheadworks\Faq\Api\Data\ArticleSearchResultsInterface;
use Aheadworks\Faq\Model\ResourceModel\Article\Collection;
use Aheadworks\Faq\Api\Data\ArticleInterface;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Faq\Model\Article as ArticleModel;

/**
 * Class ArticleRepository
 * @package Aheadworks\Faq\Model
 */
class ArticleRepository implements ArticleRepositoryInterface
{
    /**
     * @var ResourceArticle
     */
    private $resource;

    /**
     * @var ArticleFactory
     */
    private $articleFactory;

    /**
     * @var ArticleCollectionFactory
     */
    private $articleCollectionFactory;

    /**
     * @var Data\ArticleSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ArticleInterface[]
     */
    private $instancesById = [];

    /**
     * @param ResourceArticle $resource
     * @param ArticleFactory $articleFactory
     * @param ArticleCollectionFactory $articleCollectionFactory
     * @param Data\ArticleSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        ResourceArticle $resource,
        ArticleFactory $articleFactory,
        ArticleCollectionFactory $articleCollectionFactory,
        Data\ArticleSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->articleFactory = $articleFactory;
        $this->articleCollectionFactory = $articleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Save Article data
     *
     * @param ArticleInterface $article
     * @return Article
     * @throws CouldNotSaveException
     */
    public function save(ArticleInterface $article)
    {
        try {
            $this->resource->save($article);
            $this->instancesById[$article->getArticleId()] = $article;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the article: %1', $exception->getMessage())
            );
        }
        return $article;
    }

    /**
     * Load Article data by given Article Identity
     *
     * @param string $articleId
     * @return ArticleInterface
     * @throws NoSuchEntityException
     */
    public function getById($articleId)
    {
        if (!isset($this->instancesById[$articleId])) {
            /** @var Article|ArticleInterface $article */
            $article = $this->articleFactory->create();
            $this->resource->load($article, $articleId);
            if (!$article->getArticleId()) {
                throw new NoSuchEntityException(__('FAQ Article with id "%1" does not exist.', $articleId));
            }
            $this->instancesById[$articleId] = $article;
        }
        return $this->instancesById[$articleId];
    }

    /**
     * Load Article data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return ArticleSearchResultsInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var Collection $collection */
        $collection = $this->articleCollectionFactory->create();

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

        if ($criteria->getPageSize()) {
            $collection->setPageSize($criteria->getPageSize());
        }

        $items = [];
        /** @var ArticleModel $articleModel */
        foreach ($collection->getItems() as $articleModel) {
            $article = $this->articleFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $article,
                $articleModel->getData(),
                ArticleInterface::class
            );
            $items[] = $article;
        }

        /** @var ArticleSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($criteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * Delete Article
     *
     * @param Article|ArticleInterface $article
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ArticleInterface $article)
    {
        try {
            $this->resource->delete($article);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the article: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * Delete Article by given Article Id
     *
     * @param int $articleId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($articleId)
    {
        return $this->delete($this->getById($articleId));
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
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
