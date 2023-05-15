<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Faq\Api\Data\CategoryInterface;
use Aheadworks\Faq\Api\Data\ArticleInterface;
use Aheadworks\Faq\Model\Category;
use Aheadworks\Faq\Model\Article;
use Aheadworks\Faq\Model\Config;
use Aheadworks\Faq\Model\CategoryRepository;

/**
 * FAQ url model
 */
class Url
{
    /**
     * FAQ search route
     */
    const FAQ_SEARCH_ROUTE = 'search';
    
    /**
     * FAQ media path
     */
    const MEDIA_PATH = 'faq';

    /**
     * FAQ search query parameter
     */
    const FAQ_QUERY_PARAM = 'fq';

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param UrlInterface $url
     * @param Config $config
     * @param CategoryRepository $categoryRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        UrlInterface $url,
        Config $config,
        CategoryRepository $categoryRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->url = $url;
        $this->config = $config;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve Store URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * Retrieve FAQ route name
     *
     * @param int $storeId
     * @return string
     * @internal param StoreManagerInterface $store
     */
    public function getFaqRoute($storeId = null)
    {
        return $this->config->getFaqRoute($storeId);
    }

    /**
     * Retrieve FAQ base url
     *
     * @return string
     */
    public function getFaqHomeUrl()
    {
        return $this->getBaseUrl() . $this->getFaqRoute() . '/';
    }

    /**
     * Retrieve FAQ category route
     *
     * @param CategoryInterface|Category $category
     * @param int|null $storeId
     * @return string
     */
    public function getCategoryRoute($category, $storeId = null)
    {
        return $this->getFaqRoute($storeId) . '/' . $category->getUrlKey();
    }

    /**
     * Retrieve FAQ article route
     *
     * @param ArticleInterface|Article $article
     * @param int|null $storeId
     * @return string
     */
    public function getArticleRoute($article, $storeId = null)
    {
        $categoryId = $article->getCategoryId();
        $category = $this->categoryRepository->getById($categoryId);
        return $this->getCategoryRoute($category, $storeId) . '/' . $article->getUrlKey();
    }

    /**
     * Retrieve FAQ category url
     *
     * @param CategoryInterface|Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->getBaseUrl() . $this->getCategoryRoute($category);
    }
    
    /**
     * Retrieve FAQ article url
     *
     * @param ArticleInterface|Article $article
     * @return string
     */
    public function getArticleUrl($article)
    {
        $categoryId = $article->getCategoryId();
        $category = $this->categoryRepository->getById($categoryId);
        return $this->getCategoryUrl($category) . '/' . $article->getUrlKey();
    }

    /**
     * Get url of category image icon
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getCategoryIconUrl(CategoryInterface $category)
    {
        return $this->getMediaUrl($category->getCategoryIcon());
    }

    /**
     * Get an image icon url for article listing
     *
     * @param CategoryInterface $category
     * @return string
     */
    public function getArticleListIconUrl(CategoryInterface $category)
    {
        return $this->getMediaUrl($category->getArticleListIcon());
    }

    /**
     * Retrieve FAQ media url
     *
     * @param string $mediaName
     * @return string
     */
    private function getMediaUrl($mediaName)
    {
        $baseMediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $mediaName ? $baseMediaUrl . self::MEDIA_PATH . '/' . $mediaName : null;
    }

    /**
     * Retrieve FAQ search results page route
     *
     * @return string
     */
    public function getSearchResultsPageRoute()
    {
        return $this->config->getFaqRoute() . '/' . self::FAQ_SEARCH_ROUTE;
    }

    /**
     * Retrieve FAQ Search results page url
     *
     * @return string
     */
    public function getSearchResultsPageUrl()
    {
        return $this->getFaqHomeUrl() . self::FAQ_SEARCH_ROUTE;
    }
}
