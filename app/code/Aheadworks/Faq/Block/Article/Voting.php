<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Block\Article;

use Magento\Framework\View\Element\Template;
use Magento\Backend\Block\Widget\Context;
use Aheadworks\Faq\Api\ArticleRepositoryInterface;
use Aheadworks\Faq\Api\Data\ArticleInterface;
use Aheadworks\Faq\Model\Layout\Processor\LayoutProcessorInterface;

/**
 * Class Voting
 * @package Aheadworks\Faq\Block\Article
 */
class Voting extends Template
{
    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var LayoutProcessorInterface[]
     */
    private $layoutProcessors;

    /**
     * @param Context $context
     * @param ArticleRepositoryInterface $articleRepository
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        Context $context,
        ArticleRepositoryInterface $articleRepository,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->articleRepository = $articleRepository;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];
        $this->layoutProcessors = $layoutProcessors;
    }

    /**
     * Retrieve js layout config
     *
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout, $this->getArticle());
        }

        return json_encode($this->jsLayout);
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
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->getArticle()->getIdentities();
    }
}
