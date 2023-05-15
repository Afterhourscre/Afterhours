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

use Magento\Framework\Filter\FilterManager;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Mirasvit\SeoFilter\Api\Repository\RewriteRepositoryInterface;
use Mirasvit\SeoFilter\Model\Config;
use Mirasvit\SeoFilter\Model\Context;

class LabelService
{
    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var RewriteRepositoryInterface
     */
    private $rewriteRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var UrlRewriteCollectionFactory
     */
    private $urlRewriteCollectionFactory;

    /**
     * @var UrlService
     */
    private $urlService;

    /**
     * LabelService constructor.
     * @param FilterManager $filter
     * @param RewriteRepositoryInterface $rewriteRepository
     * @param UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param UrlService $urlService
     * @param Config $config
     * @param Context $context
     */
    public function __construct(
        FilterManager $filter,
        RewriteRepositoryInterface $rewriteRepository,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        UrlService $urlService,
        Config $config,
        Context $context
    ) {
        $this->filterManager               = $filter;
        $this->rewriteRepository           = $rewriteRepository;
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->urlService                  = $urlService;
        $this->config                      = $config;
        $this->context                     = $context;
    }

    /**
     * @param string $attributeCode
     * @param string  $itemValue
     * @return string|string[]
     */
    public function createLabel($attributeCode, $itemValue)
    {
        if ($this->context->isDecimalAttribute($attributeCode)) {
            $label = str_replace('-', Config::SEPARATOR_DECIMAL, $itemValue);
            $label = $attributeCode . Config::SEPARATOR_DECIMAL . $label;
        } else {
            $label = strtolower($this->filterManager->translitUrl($itemValue));
            $label = $this->getLabelWithSeparator($label);
        }

        return $label;
    }

    /**
     * @param string $label
     * @return string
     */
    private function getLabelWithSeparator($label)
    {
        $label = str_replace('__', '_', $label);

        switch ($this->config->getComplexFilterNamesSeparator()) {
            case Config::FILTER_NAME_WITHOUT_SEPARATOR:
                $label = str_replace(Config::SEPARATOR_FILTERS, '', $label);
                break;

            case Config::FILTER_NAME_BOTTOM_DASH_SEPARATOR:
                $label = str_replace(Config::SEPARATOR_FILTERS, '_', $label);
                break;

            case Config::FILTER_NAME_CAPITAL_LETTER_SEPARATOR:
                $labelExploded = explode(Config::SEPARATOR_FILTERS, $label);
                $labelExploded = array_map('ucfirst', $labelExploded);

                $label = implode('', $labelExploded);
                $label = lcfirst($label);
                break;
        }

        return $label;
    }


    /**
     * Check if "rewrite + store_id" combination already exists in mst_seo_filter_rewrite table
     *
     * @param string $label
     * @param int    $suffix
     *
     * @return string
     */
    public function uniqueLabel($label, $suffix = 0)
    {
        $newLabel = $suffix ? $label . '_' . $suffix : $label;

        $path = $this->urlService->trimCategorySuffix($this->context->getRequest()->getOriginalPathInfo());

        $possiblePath = $path . '/' . $newLabel;

        $isExists = $this->urlRewriteCollectionFactory->create()
            ->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $this->context->getStoreId())
            ->addFieldToFilter('request_path', $possiblePath)
            ->getSize();

        if ($isExists) {
            return $this->uniqueLabel($label, $suffix + 1);
        }

        $isExists = $this->rewriteRepository->getCollection()
            ->addFieldToFilter(RewriteInterface::REWRITE, $newLabel)
            ->addFieldToFilter(RewriteInterface::STORE_ID, $this->context->getStoreId())
            ->getSize();

        if ($isExists) {
            return $this->uniqueLabel($label, $suffix + 1);
        }

        return $newLabel;
    }
}
