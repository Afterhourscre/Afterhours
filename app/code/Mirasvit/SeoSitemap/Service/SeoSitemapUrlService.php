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



namespace Mirasvit\SeoSitemap\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\SeoSitemap\Model\Config;

class SeoSitemapUrlService
{
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        MagentoUrlInterface $urlManager,
        Config $config,
        Registry $registry
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig  = $scopeConfig;
        $this->urlManager   = $urlManager;
        $this->config       = $config;
        $this->registry     = $registry;
        $this->storeId      = $this->storeManager->getStore()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseRoute()
    {
        return $this->config->getFrontendSitemapBaseUrl($this->storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigUrSuffix()
    {
        return $this->scopeConfig->getValue('catalog/seo/product_url_suffix');
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl()
            . $this->getBaseRoute()
            . $this->getConfigUrSuffix();
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathInfo)
    {

        $identifier = trim($pathInfo, '/');
        $parts      = explode('/', $identifier);

        $configUrlSuffix = $this->getConfigUrSuffix();

        if (!$this->getBaseRoute()
            || ($parts[0] != $this->getBaseRoute()
                && $parts[0] != $this->getBaseRoute() . $configUrlSuffix)) {
            return false;
        }

        if (count($parts) == 1) {
            $urlKey = $parts[0];
        } else {
            return false;
        }

        if ($urlKey == $this->getBaseRoute()
            || $urlKey == $this->getBaseRoute() . $configUrlSuffix) {
            return new DataObject([
                'module_name'     => 'seositemap',
                'controller_name' => 'index',
                'action_name'     => 'index',
                'route_name'      => $urlKey,
                'params'          => [],
            ]);
        }

        return false;
    }
}