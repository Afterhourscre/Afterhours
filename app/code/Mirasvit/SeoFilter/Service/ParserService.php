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

use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Mirasvit\SeoFilter\Api\Repository\RewriteRepositoryInterface;
use Mirasvit\SeoFilter\Model\Config;
use Mirasvit\SeoFilter\Model\Context;

class ParserService
{
    const DECIMAL_FILTERS = 'decimalFilters';
    const STATIC_FILTERS  = 'staticFilters';

    /**
     * @var UrlRewriteCollectionFactory
     */
    private $urlRewrite;

    /**
     * @var UrlService
     */
    private $urlService;

    /**
     * @var RewriteRepositoryInterface
     */
    private $rewriteRepository;

    /**
     * @var Context
     */

    private $context;
  
    /**
     * @var Config
     */
    private $config;

    /**
     * ParserService constructor.
     * @param UrlRewriteCollectionFactory $urlRewrite
     * @param RewriteRepositoryInterface $rewriteRepository
     * @param UrlService $urlService
     * @param Context $context
     */
    public function __construct(
        UrlRewriteCollectionFactory $urlRewrite,
        RewriteRepositoryInterface $rewriteRepository,
        UrlService $urlService,
        Config $config,
        Context $context
    ) {
        $this->urlRewrite        = $urlRewrite;
        $this->urlService        = $urlService;
        $this->rewriteRepository = $rewriteRepository;
        $this->config            = $config;
        $this->context           = $context;
    }

    /**
     * @return false|array
     */
    public function getParams()
    {
        if ($this->isNativeRewrite()) {
            return false;
        }

        $categoryId = $this->getCategoryId();

        if (!$categoryId) {
            return false;
        }

        $filterData = $this->splitFiltersString($this->getFiltersString());

        $staticFilters  = [];
        $decimalFilters = [];

        $decimalFilters = $this->handleDecimalFilters($filterData, $decimalFilters);

        $staticFilters = $this->handleStockFilters($filterData, $staticFilters);
        $staticFilters = $this->handleRatingFilters($filterData, $staticFilters);
        $staticFilters = $this->handleSaleFilters($filterData, $staticFilters);
        $staticFilters = $this->handleNewFilters($filterData, $staticFilters);

        $rewriteCollection = $this->rewriteRepository->getCollection()
            ->addFieldToFilter(RewriteInterface::REWRITE, ['in' => $filterData])
            ->addFieldToFilter(RewriteInterface::STORE_ID, $this->context->getStoreId());

        /** @var RewriteInterface $rewrite */
        foreach ($rewriteCollection as $rewrite) {
            $attrCode = $rewrite->getAttributeCode();
            $optionId = $rewrite->getOption();

            $staticFilters[$attrCode][] = $optionId;
        }

        $params = [];

        foreach ($decimalFilters as $attr => $values) {
            $params[$attr] = implode(Config::SEPARATOR_FILTER_VALUES, $values);
        }

        foreach ($staticFilters as $attr => $values) {
            $params[$attr] = implode(Config::SEPARATOR_FILTER_VALUES, $values);
        }

        $result = [
            'category_id' => $categoryId,
            'params'      => $params,
        ];

        return $result;
    }

    /**
     * @return false|int
     */
    private function getCategoryId()
    {
        $requestString = trim($this->context->getRequest()->getPathInfo(), '/');

        $shortRequestString = substr($requestString, 0, strrpos($requestString, '/'));

        if (!$shortRequestString) {
            return false;
        }

        if ($separator = $this->config->getCustomSeparator()) {
            $shortRequestString = str_ireplace('/'. $separator, '', $shortRequestString);
        }

        if ($suffix = $this->urlService->getCategoryUrlSuffix()) {
            $shortRequestString = $shortRequestString . $suffix;
        }

        /** @var \Magento\UrlRewrite\Model\UrlRewrite $item */
        $item = $this->urlRewrite->create()
            ->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $this->context->getStoreId())
            ->addFieldToFilter('request_path', $shortRequestString)
            ->getFirstItem();

        $categoryId = $item->getEntityId();

        return $categoryId;
    }

    /**
     * @return bool
     */
    private function isNativeRewrite()
    {
        $requestString = trim($this->context->getRequest()->getPathInfo(), '/');

        $requestPathRewrite = $this->urlRewrite->create()
            ->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $this->context->getStoreId())
            ->addFieldToFilter('request_path', $requestString);

        return $requestPathRewrite->getSize() > 0 ? true : false;
    }

    /**
     * Get filter params
     *
     * @param array  $dynamicAdditionalFilter
     * @param array  $params
     * @param string $filterFrontParam
     *
     * @return array
     */
    protected function getFilterParams($dynamicAdditionalFilter, $params, $filterFrontParam)
    {
        foreach ($dynamicAdditionalFilter as $dynamicAdditionalFilterKey => $dynamicAdditionalFilterValue) {
            if (isset($params[$filterFrontParam])) {
                $params[$filterFrontParam] .= Config::SEPARATOR_FILTER_VALUES . $dynamicAdditionalFilterValue;
            } else {
                $params[$filterFrontParam] = $dynamicAdditionalFilterValue;
            }
        }

        return $params;
    }

    /**
     * @param array $filterData
     * @param array $staticFilters
     * @return mixed
     */
    private function handleStockFilters(&$filterData, $staticFilters)
    {
        $options = [
            1 => Config::LABEL_STOCK_IN,
            2 => Config::LABEL_STOCK_OUT,
        ];

        return $this->processBuiltInFilters(Config::FILTER_STOCK, $options, $filterData, $staticFilters);
    }

    /**
     * @param array $filterData
     * @param array $staticFilters
     * @return mixed
     */
    private function handleRatingFilters(&$filterData, $staticFilters)
    {
        $options = [
            1 => Config::LABEL_RATING_1,
            2 => Config::LABEL_RATING_2,
            3 => Config::LABEL_RATING_3,
            4 => Config::LABEL_RATING_4,
            5 => Config::LABEL_RATING_5,
        ];

        return $this->processBuiltInFilters(Config::FILTER_RATING, $options, $filterData, $staticFilters);
    }

    /**
     * @param array $filterData
     * @param array $staticFilters
     * @return mixed
     */
    private function handleSaleFilters(&$filterData, $staticFilters)
    {
        $options = [
            1 => Config::FILTER_SALE,
        ];

        return $this->processBuiltInFilters(Config::FILTER_SALE, $options, $filterData, $staticFilters);
    }

    /**
     * @param array $filterData
     * @param array $staticFilters
     * @return mixed
     */
    private function handleNewFilters(&$filterData, $staticFilters)
    {
        $options = [
            1 => Config::FILTER_NEW,
        ];

        return $this->processBuiltInFilters(Config::FILTER_NEW, $options, $filterData, $staticFilters);
    }

    /**
     * @param string $attrCode
     * @param array $options
     * @param array $filterData
     * @param array $staticFilters
     * @return mixed
     */
    private function processBuiltInFilters($attrCode, $options, &$filterData, $staticFilters)
    {
        foreach ($options as $key => $label) {
            foreach ($filterData as $fKey => $value) {
                if ($value == $label) {
                    $staticFilters[$attrCode][] = $key;

                    unset($filterData[$fKey]);
                }
            }
        }

        return $staticFilters;
    }

    /**
     * @param array $filterData
     * @param array $decimalFilters
     * @return mixed
     */
    private function handleDecimalFilters(&$filterData, $decimalFilters)
    {
        foreach ($filterData as $key => $filterValue) {
            if (strpos($filterValue, Config::SEPARATOR_DECIMAL) !== false) {
                $exploded = explode(Config::SEPARATOR_DECIMAL, $filterValue);
                $attrCode = $exploded[0];
                unset($exploded[0]);

                $option = implode(Config::SEPARATOR_FILTERS, $exploded);

                $decimalFilters[$attrCode][] = $option;

                unset($filterData[$key]);
            }
        }

        return $decimalFilters;
    }

    /**
     * @return string
     */
    private function getFiltersString()
    {
        $uri = trim($this->context->getRequest()->getPathInfo(), '/');

        $filterString = substr($uri, strrpos($uri, '/') + 1);

        $suffix = $this->urlService->getCategoryUrlSuffix();
        if ($suffix && substr($filterString, -strlen($suffix)) === $suffix) {
            $filterString = substr($filterString, 0, -strlen($suffix));
        }

        return $filterString;
    }

    /**
     * @param string $filtersString
     *
     * @return array
     */
    private function splitFiltersString($filtersString)
    {
        $filterInfo = explode(Config::SEPARATOR_FILTERS, $filtersString);
        foreach ($filterInfo as $key => $value) {
            $filterInfo[$key] = $value;
        }

        $filterInfo = array_diff($filterInfo, ['', null, false]);

        return $filterInfo;
    }
}
