<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Controller\Category;

use Aheadworks\Faq\Controller\AbstractAction;
use Aheadworks\Faq\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;
use Aheadworks\Faq\Model\Url;
use Aheadworks\Faq\Model\Category;
use Aheadworks\Faq\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;

/**
 * FAQ category page view
 */
class Index extends AbstractAction
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

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
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Url $url,
        Config $config,
        Context $context,
        PageFactory $resultPageFactory,
        CategoryRepositoryInterface $categoryRepository,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $storeManager);
        $this->url = $url;
        $this->config = $config;
        $this->resultPageFactory = $resultPageFactory;
        $this->categoryRepository = $categoryRepository;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->config = $config;
    }

    /**
     * View FAQ category page action
     *
     * @return Page|Redirect|Forward
     */
    public function _execute()
    {
        $categoryId = $this->getRequest()->getParam('id');
        
        /** @var Category $category */
        $category = $this->categoryRepository->getById($categoryId);

        if (!$category->getIsEnable()) {
            /** @var Forward $forward */
            $forward = $this->resultForwardFactory->create();
            return $forward->setModule('cms')->setController('noroute')->forward('index');
        }

        if (!array_intersect($category->getStoreIds(), $this->getCurrentStores())) {
            return $this->redirectWithErrorMessage();
        }

        $metaTitle = $category->getMetaTitle() ? $category->getMetaTitle() : $category->getName();
        
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($metaTitle);
        $resultPage->getConfig()->setDescription($category->getMetaDescription());
        $resultPage->getLayout()->getBlock('breadcrumbs')
            ->addCrumb(
                'home',
                [
                    'label' => 'Home',
                    'title'=>__('Go to store homepage'),
                    'link'=> $this->url->getBaseUrl()
                ]
            )
            ->addCrumb(
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
                ]
            );
        
        return $resultPage;
    }
}
