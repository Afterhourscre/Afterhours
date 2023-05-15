<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model;

use Aheadworks\Faq\Api\Data\ArticleInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrderBuilder;
use Aheadworks\Faq\Api\SearchManagementInterface;
use Aheadworks\Faq\Api\Data\ArticleSearchResultsInterface;
use Aheadworks\Faq\Api\Data\ArticleSearchResultsInterfaceFactory;
use Aheadworks\Faq\Model\ResourceModel\Search as SearchResource;

/**
 * Class Search
 * @package Aheadworks\Faq\Model
 */
class Search implements SearchManagementInterface
{
    /**
     * @var ArticleSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var SearchResource
     */
    private $searchResource;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param ArticleSearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchResource $searchResource
     * @param ArticleRepository $articleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        ArticleSearchResultsInterfaceFactory $searchResultsFactory,
        SearchResource $searchResource,
        ArticleRepository $articleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->articleRepository = $articleRepository;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchResource = $searchResource;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * Make Full Text Search and return found Articles
     *
     * @param string $searchString
     * @param int $storeId
     * @param int|null $limit
     * @return ArticleSearchResultsInterface
     * @internal param SearchCriteriaInterface $searchCriteria
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function searchArticles($searchString, $storeId, $limit = null)
    {
        if (!$searchString || empty($articlesIds = $this->searchResource->searchQuery($searchString, $limit))) {
            return $this->searchResultsFactory->create()->setItems([]);
        }

        $sortVotes = $this->sortOrderBuilder
            ->setField(ArticleInterface::VOTES_YES)
            ->setDescendingDirection()
            ->create();

        /** \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ArticleInterface::STORE_IDS, $storeId, 'in')
            ->addFilter(ArticleInterface::IS_ENABLE, true)
            ->addFilter(ArticleInterface::ARTICLE_ID, $articlesIds, 'in')
            ->addSortOrder($sortVotes)
            ->create();

        return $this->articleRepository->getList($searchCriteria);
    }
}
