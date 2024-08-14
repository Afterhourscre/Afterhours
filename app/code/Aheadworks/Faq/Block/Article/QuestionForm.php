<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Block\Article;

use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\Faq\Api\ArticleRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Faq\Model\Config;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;

/**
 * Class QuestionForm
 * @package Aheadworks\Faq\Block\Article
 */
class QuestionForm extends Template
{
    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param ArticleRepositoryInterface $articleRepository
     * @param HttpContext $httpContext
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArticleRepositoryInterface $articleRepository,
        HttpContext $httpContext,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->articleRepository = $articleRepository;
        $this->httpContext = $httpContext;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->config->getIsEnableQuestionForm()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Retrieve article title
     *
     * @return string
     */
    public function getArticleTitle()
    {
        $articleId = $this->getRequest()->getParam('id');

        try {
            $article = $this->articleRepository->getById($articleId);
            $title = $article->getTitle();
        } catch (LocalizedException $e) {
            $title = '';
        }
        return $title;
    }

    /**
     * Get Email value
     *
     * @return string
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    /**
     * Get form action
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('faq/article/question', ['_secure' => true]);
    }
}
