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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.16
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\SeoFilter\Model\Context;

class UrlService
{
    /**
     * Cache for category rewrite suffix
     * @var array
     */
    protected $categoryUrlSuffix = [];

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Context
     */
    private $context;

    /**
     * UrlService constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        Context $context
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig  = $scopeConfig;
        $this->registry     = $registry;
        $this->context      = $context;
    }

    /**
     * Retrieve category rewrite suffix for store
     *
     * @param null|int $storeId
     *
     * @return string
     */
    public function getCategoryUrlSuffix($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        if (!isset($this->categoryUrlSuffix[$storeId])) {
            $this->categoryUrlSuffix[$storeId] = $this->scopeConfig->getValue(
                \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return $this->categoryUrlSuffix[$storeId];
    }

    /**
     * @param string $url
     * @return string|string[]
     */
    public function trimCategorySuffix($url)
    {
        $suffix = $this->getCategoryUrlSuffix();

        if ($suffix && $suffix !== '/') {
            $url = str_replace($suffix, '', $url);
        }

        return $url;
    }

    /**
     * Return catalog current category object
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @param bool|string $url
     *
     * @return string
     */
    public function getQueryParams($url = false)
    {
        $currentUrl = $this->context->getUrlBuilder()->getCurrentUrl();

        if ($url) {
            return strtok($currentUrl, '?') . strstr($url, '?', false);
        }

        return strstr($currentUrl, '?', false);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function addUrlParams($url)
    {
        return $url . $this->getQueryParams();
    }
}
