<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Block\Article;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\Faq\Api\ArticleRepositoryInterface;
use Aheadworks\Faq\Api\Data\ArticleInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Article
 * @package Aheadworks\Faq\Block\Article
 */
class Article extends Template implements IdentityInterface
{
    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @param Context $context
     * @param ArticleRepositoryInterface $articleRepository
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArticleRepositoryInterface $articleRepository,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->articleRepository = $articleRepository;
        $this->filterProvider = $filterProvider;
    }
    
    /**
     * Retrieve article instance
     *
     * @return ArticleInterface
     */
    public function getArticle()
    {
        $articleId = $this->getRequest()->getParam('id');

        return $this->articleRepository->getById($articleId);
    }

    /**
     * Retrieve article title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getArticle()->getTitle();
    }

    /**
     * Retrieve article content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->filterProvider->getPageFilter()->filter($this->getArticle()->getContent());
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->getArticle()->getIdentities();
    }
}
