<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Block\Category;

use Aheadworks\Faq\Block\AbstractTemplate;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Api\SortOrderBuilder;
use Aheadworks\Faq\Api\CategoryRepositoryInterface;
use Aheadworks\Faq\Api\ArticleRepositoryInterface;
use Aheadworks\Faq\Model\Config;
use Aheadworks\Faq\Api\Data\ArticleInterface;
use Aheadworks\Faq\Api\Data\ArticleSearchResultsInterface;
use Aheadworks\Faq\Model\Url;
use Aheadworks\Faq\Api\Data\CategoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * FAQ Category page block
 *
 * Class Category
 * @package Aheadworks\Faq\Block\Category
 */
class Category extends AbstractTemplate
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ArticleRepositoryInterface $articleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param Url $url
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository,
        ArticleRepositoryInterface $articleRepository,
        SortOrderBuilder $sortOrderBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config,
        Url $url,
        array $data = []
    ) {
        parent::__construct($context, $url, $config, $data);
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->articleRepository = $articleRepository;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Retrieve category instance
     *
     * @return CategoryInterface|bool
     */
    public function getCategory()
    {
        $categoryId = $this->getRequest()->getParam('id');

        try {
            $category = $this->categoryRepository->getById($categoryId);
        } catch (LocalizedException $e) {
            return false;
        }
        return $category;
    }

    /**
     * Retrieve category title
     *
     * @return string
     */
    public function getTitle()
    {
        $category = $this->getCategory();
        return $category ? $category->getName() : '';
    }

    /**
     * Retrieve article list of certain category
     *
     * @return ArticleSearchResultsInterface[]
     */
    public function getCategoryArticles()
    {
        $categoryId = $this->getRequest()->getParam('id');
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
            ->setSortOrders([$sortOrder, $sortVotes])
            ->create();
        /** @var ArticleSearchResultsInterface $articleList */
        $searchResults = $this->articleRepository->getList($searchCriteria);

        return $searchResults->getItems();
    }
}
