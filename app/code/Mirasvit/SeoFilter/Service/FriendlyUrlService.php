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

use Mirasvit\SeoFilter\Model\Config;
use Mirasvit\SeoFilter\Model\Context;

class FriendlyUrlService
{
    /**
     * @var RewriteService
     */
    private $rewriteService;

    /**
     * @var UrlService
     */
    private $urlService;

    /**
     * @var Context
     */

    private $context;

    /**
     * @var Config
     */
    private $config;
    
    /**
     * FriendlyUrlService constructor.
     * @param RewriteService $rewrite
     * @param UrlService $urlService
     * @param Config $config
     * @param Context $context
     */
    public function __construct(
        RewriteService $rewrite,
        UrlService $urlService,
        Config $config,
        Context $context
    ) {
        $this->rewriteService = $rewrite;
        $this->urlService     = $urlService;
        $this->config         = $config;
        $this->context        = $context;
    }

    /**
     * Create friendly urls for Layered Navigation (add and remove filters)
     *
     * @param string $attributeCode
     * @param string $filterValue ex: 100  50-60,60-   234,203
     * @param bool   $remove
     *
     * @return string|false
     */
    public function getUrl($attributeCode, $filterValue, $remove = false)
    {
        if (is_array($filterValue)) {
            $values = $filterValue;
        } else {
            $values = explode(Config::SEPARATOR_FILTER_VALUES, $filterValue);
        }

        $requiredFilters[$attributeCode] = [];

        foreach ($values as $value) {
            $requiredFilters[$attributeCode][$value] = $value;
        }

        // merge with previous filters
        foreach ($this->rewriteService->getActiveFilters() as $attr => $filters) {
            foreach ($filters as $filter) {
                $requiredFilters[$attr][$filter] = $filter;
            }
        }

        // remove filter
        if ($remove && isset($requiredFilters[$attributeCode])) {
            foreach ($values as $value) {
                unset($requiredFilters[$attributeCode][$value]);
            }
        }

        // merge all filters on one line f1-f2-f3-f4
        $filterLines = [];
        foreach ($requiredFilters as $attrCode => $filters) {
            $filterLine = [];
            foreach ($filters as $filter) {
                $filterLine[] = $this->rewriteService->getRewrite($attrCode, $filter);
            }

            if (count($filterLine)) {
                $filterLines[] = implode(Config::SEPARATOR_FILTERS, $filterLine);
            }
        }
        $filterLines = implode(Config::SEPARATOR_FILTERS, $filterLines);

        //sort filters
        $values = explode(Config::SEPARATOR_FILTERS, $filterLines);
        asort($values);
        $filterString = implode(Config::SEPARATOR_FILTERS, $values);

        $url = $this->getPreparedCurrentCategoryUrl($filterString);

        return $url;
    }

    /**
     * @param string $filterUrlString
     *
     * @return string
     */
    public function getPreparedCurrentCategoryUrl($filterUrlString)
    {
        $suffix = $this->urlService->getCategoryUrlSuffix();
        $url    = $this->context->getCurrentCategory()->getUrl();
        $url    = preg_replace('/\?.*/', '', $url);
        $url    = ($suffix && $suffix !== '/') ? str_replace($suffix, '', $url) : $url;
        if (!empty($filterUrlString)) {
            if ($separator = $this->config->getCustomSeparator()) {
                $url .= (substr($url, -1, 1) === '/' ? '' : '/') . $separator;
            }

            $url .= (substr($url, -1, 1) === '/' ? '' : '/') . $filterUrlString;
        }
        $url = $url . $suffix;

        return $url;
    }
}
