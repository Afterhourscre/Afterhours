<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Controller\Article;

use Aheadworks\Faq\Controller\AbstractAction;
use Aheadworks\Faq\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;
use Aheadworks\Faq\Model\Url;
use Aheadworks\Faq\Model\Article;
use Aheadworks\Faq\Model\Category;
use Aheadworks\Faq\Api\ArticleRepositoryInterface;
use Aheadworks\Faq\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;

/**
 * FAQ article page view
 */
class Index extends AbstractAction
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ArticleRepositoryInterface
     */
    private $articleRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var Url
     */
    private $url;

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @param Url $url
     * @param Config $config
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ArticleRepositoryInterface $articleRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Url $url,
        Config $config,
        Context $context,
        PageFactory $resultPageFactory,
        ArticleRepositoryInterface $articleRepository,
        CategoryRepositoryInterface $categoryRepository,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $storeManager);
        $this->url = $url;
        $this->config = $config;
        $this->resultPageFactory = $resultPageFactory;
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * View FAQ article page action
     *
     * @return Page|Redirect|Forward
     */
    public function _execute()
    {
        $articleId = $this->getRequest()->getParam('id');

        /** @var Article $article */
        $article = $this->articleRepository->getById($articleId);
        if (!$article->getIsEnable()) {
            /** @var Forward $forward */
            $forward = $this->resultForwardFactory->create();
            return $forward->setModule('cms')->setController('noroute')->forward('index');
        }

        if (!array_intersect($article->getStoreIds(), $this->getCurrentStores())) {
            return $this->redirectWithErrorMessage();
        }

        $metaTitle = $article->getMetaTitle() ? $article->getMetaTitle() : $article->getTitle();
        /** @var Category $category */
        $category = $this->categoryRepository->getById($article->getCategoryId());

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($metaTitle);
        $resultPage->getConfig()->setDescription($article->getMetaDescription());
        $resultPage->getLayout()->getBlock('breadcrumbs')
            ->addCrumb(
                'home',
                [
                    'label' => 'Home',
                    'title'=>__('Go to store homepage'),
                    'link'=> $this->url->getBaseUrl()
                ]
            )->addCrumb(
                'faq',
                [
                    'label' => $this->config->getFaqName(),
                    'title'=>__('Go to %1', $this->config->getFaqName()),
                    'link'=> $this->url->getFaqHomeUrl()
                ]
            )->addCrumb(
                'category',
                [
                    'label' => $category->getName(),
                    'title'=>__('Go to %1', $category->getName()),
                    'link'=> $this->url->getCategoryUrl($category)
                ]
            )->addCrumb(
                'article',
                [
                    'label' => $article->getTitle()
                ]
            );

        return $resultPage;
    }
}
