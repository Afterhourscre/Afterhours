<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\Faq\Model\Url;
use Aheadworks\Faq\Model\Config;
use Aheadworks\Faq\Model\Article;
use Aheadworks\Faq\Api\Data\CategoryInterface;
use Aheadworks\Faq\Model\Category;
use Aheadworks\Faq\Api\Data\ArticleInterface;

/**
 * Class AbstractTemplate
 * @package Aheadworks\Faq\Block
 */
class AbstractTemplate extends Template
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param Url $url
     * @param array $data
     */
    public function __construct(
        Context $context,
        Url $url,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->url = $url;
        $this->config = $config;
    }

    /**
     * Get current stores
     *
     * @return int
     */
    protected function getCurrentStore()
    {
        return (int)$this->_storeManager->getStore()->getId();
    }

    /**
     * Retrieve icon URL for article list
     *
     * @param CategoryInterface|Category $category
     * @return string
     */
    public function getArticleListIconUrl($category)
    {
        return $this->url->getArticleListIconUrl($category);
    }

    /**
     * Retrieve article URL
     *
     * @param ArticleInterface|Article $article
     * @return string
     */
    public function getArticleUrl($article)
    {
        return $this->url->getArticleUrl($article);
    }

    /**
     * Retrieve category icon URL
     *
     * @param CategoryInterface|Category $category
     * @return string
     */
    public function getCategoryIconUrl($category)
    {
        return $this->url->getCategoryIconUrl($category);
    }

    /**
     * Retrieve category URL
     *
     * @param CategoryInterface|Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->url->getCategoryUrl($category);
    }

    /**
     * Retrieve FAQ Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->config->getFaqName();
    }

    /**
     * Retrieve FAQ Search results page url
     *
     * @return string
     */
    public function getFaqSearchUrl()
    {
        return $this->url->getSearchResultsPageUrl();
    }
}
