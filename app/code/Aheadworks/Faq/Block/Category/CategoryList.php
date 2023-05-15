<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Block\Category;

use Aheadworks\Faq\Block\AbstractTemplate;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Api\SortOrderBuilder;
use Aheadworks\Faq\Model\Url;
use Aheadworks\Faq\Model\Config;
use Aheadworks\Faq\Model\Category as CategoryModel;
use Aheadworks\Faq\Api\Data\CategoryInterface;
use Aheadworks\Faq\Api\Data\ArticleInterface;
use Aheadworks\Faq\Api\CategoryRepositoryInterface;
use Aheadworks\Faq\Api\ArticleRepositoryInterface;
use Aheadworks\Faq\Api\Data\ArticleSearchResultsInterface;
use Aheadworks\Faq\Api\Data\CategorySearchResultsInterface;

/**
 * FAQ Category list
 *
 * Class CategoryList
 * @package Aheadworks\Faq\Block\Category
 */
class CategoryList extends AbstractTemplate
{
    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var array
     */
    private $moreArticleList = [];

    /**
     * @param Context $context
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ArticleRepositoryInterface $articleRepository
     * @param SortOrderBuilder $sortOrderBuilder
     * @param Url $url
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CategoryRepositoryInterface $categoryRepository,
        ArticleRepositoryInterface $articleRepository,
        SortOrderBuilder $sortOrderBuilder,
        Url $url,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $url, $config, $data);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Retrieve categories
     *
     * @return array
     */
    public function getCategories()
    {
        /** \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $this->sortOrderBuilder
            ->setField(CategoryInterface::SORT_ORDER)
            ->setAscendingDirection()
            ->create();

        /** \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::IS_ENABLE, true)
            ->addFilter(CategoryInterface::STORE_IDS, $this->getCurrentStore())
            ->addSortOrder($sortOrder)
            ->create();

        /** @var CategorySearchResultsInterface $articleList */
        $searchResults = $this->categoryRepository->getList($searchCriteria);

        return $searchResults->getItems();
    }

    /**
     * Retrieve articles of one category
     *
     * @param CategoryInterface|CategoryModel $category
     * @return array
     */
    public function getCategoryArticles($category)
    {
        $categoryId = $category->getCategoryId();
        $limit = $category->getNumArticlesToDisplay() ? $category->getNumArticlesToDisplay() : 0;

        /** \Magento\Framework\Api\SortOrder $sortOrder */
        $sortOrder = $this->sortOrderBuilder
            ->setField(ArticleInterface::SORT_ORDER)
            ->setAscendingDirection()
            ->create();
        $sortVotes = $this->sortOrderBuilder
            ->setField(ArticleInterface::VOTES_YES)
            ->setDescendingDirection()
            ->create();
        /** \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('category_id', $categoryId)
            ->addFilter(ArticleInterface::IS_ENABLE, true)
            ->addFilter(ArticleInterface::STORE_IDS, $this->getCurrentStore())
            ->setPageSize($limit)
            ->setSortOrders([$sortOrder, $sortVotes])
            ->create();

        /** @var ArticleSearchResultsInterface $articleList */
        $searchResults = $this->articleRepository->getList($searchCriteria);

        $this->moreArticleList[$category->getId()] =
            $searchResults->getTotalCount() - count($searchResults->getItems());

        return $searchResults->getItems();
    }

    /**
     * Get "Read more" number of articles
     *
     * @param int $categoryId
     * @return int
     */
    public function getMoreArticlesNumber($categoryId)
    {
        return isset($this->moreArticleList[$categoryId])
            ? $this->moreArticleList[$categoryId]
            : 0;
    }
}
