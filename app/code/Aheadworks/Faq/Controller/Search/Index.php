<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Controller\Search;

use Aheadworks\Faq\Controller\AbstractAction;
use Magento\Framework\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\Redirect;
use Aheadworks\Faq\Model\Url;
use Aheadworks\Faq\Model\Config;
use Aheadworks\Faq\Api\SearchManagementInterface;
use Aheadworks\Faq\Api\ArticleRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;

/**
 * FAQ search results view
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
     * @var SearchManagementInterface
     */
    private $search;

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
     * @param SearchManagementInterface $searchManagementInterface
     * @param StoreManagerInterface $storeManager
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Url $url,
        Config $config,
        Context $context,
        PageFactory $resultPageFactory,
        ArticleRepositoryInterface $articleRepository,
        SearchManagementInterface $searchManagementInterface,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $storeManager);
        $this->url = $url;
        $this->config = $config;
        $this->search = $searchManagementInterface;
        $this->resultPageFactory = $resultPageFactory;
        $this->articleRepository = $articleRepository;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * View FAQ search results action
     *
     * @return Page|Redirect|Forward
     */
    public function _execute()
    {
        if (!$this->config->isFaqSearchEnabled()) {
            /** @var Forward $forward */
            $forward = $this->resultForwardFactory->create();
            return $forward->setModule('cms')->setController('noroute')->forward('index');
        }

        $searchQuery = $this->getRequest()->getParam(Url::FAQ_QUERY_PARAM);
        $prepareQuery = $this->prepareSearchQuery($searchQuery);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $searchResultsBlock = $resultPage->getLayout()->getBlock('aw_faq.search_results');
        $searchResults = $this->getArticles($prepareQuery);
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
                'search',
                [
                    'label' => __('Search results for: "%1"', $searchQuery)
                ]
            );

        if (!$prepareQuery || !$searchResults) {
            $searchResultsBlock->setBackUrl($this->_redirect->getRefererUrl());
        }

        $searchResultsBlock->setSearchResults($searchResults);
        $pageConfig = $resultPage->getConfig();
        $pageConfig->getTitle()->set(__('Search results for: "%1"', $searchQuery));
        $pageConfig->setMetadata('robots', 'noindex');

        return $resultPage;
    }

    /**
     * Retrieve all articles
     *
     * @param string $searchQuery
     * @return array
     */
    private function getArticles($searchQuery)
    {
        return $this->search->searchArticles($searchQuery, $this->getCurrentStore())->getItems();
    }

    /**
     * Prepare Search Query for
     * safe using in database select
     *
     * @param string $searchQuery
     * @return string
     */
    private function prepareSearchQuery($searchQuery)
    {
        $query = preg_split('/[\s*\W*]/', strip_tags($searchQuery));
        $searchWords = [];

        foreach ($query as $word) {
            if (mb_strlen($word) > 2) {
                $searchWords[] = trim($word) . '*';
            }
        }

        return implode(' ', $searchWords);
    }
}
