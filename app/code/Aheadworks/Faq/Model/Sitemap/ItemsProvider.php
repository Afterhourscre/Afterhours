<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Model\Sitemap;

use Aheadworks\Faq\Api\ArticleRepositoryInterface;
use Aheadworks\Faq\Api\CategoryRepositoryInterface;
use Aheadworks\Faq\Api\Data\CategoryInterface;
use Aheadworks\Faq\Api\Data\ArticleInterface;
use Aheadworks\Faq\Model\Config;
use Aheadworks\Faq\Model\Url;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class ItemsProvider
 * @package Aheadworks\Faq\Model\Sitemap
 */
class ItemsProvider
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ArticleRepositoryInterface $articleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Url $url
     * @param Config $config
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        ArticleRepositoryInterface $articleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Url $url,
        Config $config
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->url = $url;
        $this->config = $config;
    }

    /**
     * Retrieves FAQ homepage sitemap item
     *
     * @param int $storeId
     * @return DataObject
     */
    public function getFaqHomePageItem($storeId)
    {
        return new DataObject(
            [
                'changefreq' => $this->getChangeFreq($storeId),
                'priority' => $this->getPriority($storeId),
                'collection' => [
                    new DataObject(
                        [
                            'id' => 'faq_home',
                            'url' => $this->url->getFaqRoute($storeId),
                            'updated_at' => $this->getCurrentDateTime()
                        ]
                    )
                ]
            ]
        );
    }

    /**
     * Retrieves FAQ category sitemap items
     *
     * @param int $storeId
     * @return DataObject
     */
    public function getCategoryItems($storeId)
    {
        $categoryItems = [];
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(CategoryInterface::IS_ENABLE, true)
            ->addFilter(CategoryInterface::STORE_IDS, $storeId)
            ->create();
        $categories = $this->categoryRepository->getList($searchCriteria)
            ->getItems();
        foreach ($categories as $category) {
            $categoryItems[$category->getCategoryId()] = new DataObject(
                [
                    'id' => $category->getCategoryId(),
                    'url' => $this->url->getCategoryRoute($category, $storeId),
                    'updated_at' => $this->getCurrentDateTime()
                ]
            );
        }
        return new DataObject(
            [
                'changefreq' => $this->getChangeFreq($storeId),
                'priority' => $this->getPriority($storeId),
                'collection' => $categoryItems
            ]
        );
    }

    /**
     * Retrieves FAQ article sitemap items
     *
     * @param int $storeId
     * @return DataObject
     */
    public function getArticleItems($storeId)
    {
        $articleItems = [];
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(ArticleInterface::IS_ENABLE, true)
            ->addFilter(ArticleInterface::STORE_IDS, $storeId)
            ->create();
        $articles = $this->articleRepository->getList($searchCriteria)
            ->getItems();
        foreach ($articles as $article) {
            $articleItems[$article->getArticleId()] = new DataObject(
                [
                    'id' => $article->getArticleId(),
                    'url' => $this->url->getArticleRoute($article, $storeId),
                    'updated_at' => $this->getCurrentDateTime()
                ]
            );
        }
        return new DataObject(
            [
                'changefreq' => $this->getChangeFreq($storeId),
                'priority' => $this->getPriority($storeId),
                'collection' => $articleItems
            ]
        );
    }

    /**
     * Get change frequency
     *
     * @param int $storeId
     * @return float
     */
    private function getChangeFreq($storeId)
    {
        return $this->config->getSitemapChangeFrequency($storeId);
    }

    /**
     * Get priority
     *
     * @param int $storeId
     * @return string
     */
    private function getPriority($storeId)
    {
        return $this->config->getSitemapPriority($storeId);
    }

    /**
     * Current date/time
     *
     * @return string
     */
    private function getCurrentDateTime()
    {
        return (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT);
    }
}
