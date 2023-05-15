<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Controller\Index;

use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Aheadworks\Faq\Controller\AbstractAction;
use Aheadworks\Faq\Model\Config;
use Aheadworks\Faq\Model\Url;
use Magento\Store\Model\StoreManagerInterface;

/**
 * FAQ home page view
 */
class Index extends AbstractAction
{
    /**
     * @var Url
     */
    private $url;
    
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @param Url $url
     * @param Config $config
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param StoreManagerInterface $storeManager
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Url $url,
        Config $config,
        Context $context,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context, $storeManager);
        $this->url = $url;
        $this->config = $config;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * View FAQ homepage action
     *
     * @return Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function _execute()
    {
        $path = explode('/', trim($this->getRequest()->getPathInfo(), '/'));
        if ($this->config->getFaqRoute() !== $path[0]) {
            /** @var Forward $forward */
            $forward = $this->resultForwardFactory->create();
            return $forward->setModule('cms')->setController('noroute')->forward('index');
        }
        
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $pageConfig = $resultPage->getConfig();
        $pageConfig->getTitle()->set($this->config->getFaqMetaTitle());
        $pageConfig->setDescription($this->config->getFaqMetaDescription());
        $pageConfig->addBodyClass('aw-columns-' . $this->config->getDefaultNumberOfColumnsToDisplay());
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
                    'label' => $this->config->getFaqName()
                ]
            );
        return $resultPage;
    }
}
