<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.169
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoSitemap\Block;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\SeoSitemap\Api\Repository\ProviderInterface;
use Mirasvit\SeoSitemap\Block\Map\Pager;
use Mirasvit\SeoSitemap\Model\Config;
use Mirasvit\SeoSitemap\Model\Pager\Collection as PagerCollection;
use Mirasvit\SeoSitemap\Repository\ProviderRepository;
use Mirasvit\SeoSitemap\Service\SeoSitemapCategoryProductService;

class Map extends Template
{
    private   $providerRepository;

    private   $categoryProductService;

    private   $pagerCollection;

    private   $config;

    private   $context;

    protected $pageConfig;

    public function __construct(
        ProviderRepository $providerRepository,
        SeoSitemapCategoryProductService $categoryProductService,
        PagerCollection $pagerCollection,
        Config $config,
        Context $context
    ) {
        $this->providerRepository     = $providerRepository;
        $this->categoryProductService = $categoryProductService;
        $this->pagerCollection        = $pagerCollection;
        $this->config                 = $config;
        $this->context                = $context;
        $this->pageConfig             = $context->getPageConfig();

        parent::__construct($context, []);
    }

    /**
     * Prepare breadcrumbs
     * @return void
     */
    protected function addBreadcrumbs()
    {
        if ($this->config->canShowBreadcrumbs()
            && $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->context->getStoreManager()->getStore()->getBaseUrl(),
            ]);
            $breadcrumbsBlock->addCrumb('cms_page', [
                'label' => $this->config->getFrontendSitemapMetaTitle(),
                'title' => $this->config->getFrontendSitemapMetaTitle(),
            ]);
        }
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->addBreadcrumbs();
        $this->pageConfig->getTitle()->set($this->config->getFrontendSitemapMetaTitle());
        $this->pageConfig->setKeywords($this->config->getFrontendSitemapMetaKeywords());
        $this->pageConfig->setDescription($this->config->getFrontendSitemapMetaDescription());

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($this->escapeHtml($this->config->getFrontendSitemapH1()));
        }

        if ($this->getLimitPerPage()) {
            $this->getPagerCollection()->setPageSize($this->getLimitPerPage());
            /** @var Pager $pagerBlock */
            $pagerBlock = $this->getLayout()->getBlock('map.pager');
            $pagerBlock->setCollection($this->getPagerCollection());
            $pagerBlock->setShowPerPage(false);
            $pagerBlock->setShowAmounts(false);
            $pagerBlock->setLimit($this->getLimitPerPage());
        } else {
            $this->getLayout()->unsetElement('map.pager');
            $this->getPagerCollection();
        }

        $this->setCategoryBlockCollection();

        return parent::_prepareLayout();
    }

    /**
     * @return \Mirasvit\SeoSitemap\Model\Pager\Collection
     */
    private function getPagerCollection()
    {
        if (empty($this->collection)) {
            $this->pagerCollection->setCollection($this->categoryProductService->getCategoryProductsTree());
            $this->collection = $this->pagerCollection;
        }

        return $this->collection;
    }

    public function getCollection()
    {
        if (empty($this->collection)) {
            $this->getPagerCollection();
        }

        return $this->collection->getCollection();
    }

    /**
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getPagerCollection()->getCurPage() == 1;
    }

    /**
     * @return int
     */
    public function getLimitPerPage()
    {
        return (int)$this->config->getFrontendLinksLimit();
    }

    private function setCategoryBlockCollection()
    {
        $categoryBlock = $this->getChildBlock('map.category');
        $categoryBlock->setCollection($this->getCollection());
    }

    /**
     * @return ProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providerRepository->getProviders();
    }

    /**
     * @param ProviderInterface $provider
     *
     * @return DataObject[]
     */
    public function getProviderItems(ProviderInterface $provider)
    {
        return $provider->getItems($this->_storeManager->getStore()->getId());
    }
}
