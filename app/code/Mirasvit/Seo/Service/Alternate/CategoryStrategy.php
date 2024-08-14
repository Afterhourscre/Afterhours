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
 * @version   2.9.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Seo\Service\Alternate;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Registry;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Mirasvit\Seo\Api\Service\Alternate\UrlInterface;
use Mirasvit\Seo\Api\Config\AlternateConfigInterface;

class CategoryStrategy implements \Mirasvit\Seo\Api\Service\Alternate\StrategyInterface
{
    protected $url;

    protected $alternateConfig;

    protected $context;

    protected $categoryCollectionFactory;

    protected $categoryFactory;

    protected $registry;

    protected $urlRewriteFactory;

    protected $objectManager;

    public function __construct(
        UrlInterface $url,
        AlternateConfigInterface $alternateConfig,
        Context $context,
        CollectionFactory $categoryCollectionFactory,
        CategoryFactory $categoryFactory,
        Registry $registry,
        UrlRewriteCollectionFactory $urlRewriteFactory,
        ObjectManagerInterface $objectManager
    ) {
        $this->url                       = $url;
        $this->alternateConfig           = $alternateConfig;
        $this->context                   = $context;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryFactory           = $categoryFactory;
        $this->registry                  = $registry;
        $this->urlRewriteFactory         = $urlRewriteFactory;
        $this->objectManager             = $objectManager;
    }

    public function getStoreUrls(): array
    {
        $storeUrls = $this->url->getStoresCurrentUrl();
        $storeUrls = $this->getAlternateUrl($storeUrls);

        return $storeUrls;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getAlternateUrl(array $storeUrls): array
    {
        $currentBaseUrl = $this->context->getUrlBuilder()->getBaseUrl();
        foreach ($this->url->getStores() as $storeId => $store) {
            $currentUrl = $this->context->getUrlBuilder()->getCurrentUrl();
            $category   = $this->categoryCollectionFactory->create()
                ->setStoreId($store->getId())
                ->addFieldToFilter('is_active', ['eq' => '1'])
                ->addFieldToFilter('entity_id', ['eq' => $this->registry->registry('current_category')->getId()])
                ->getFirstItem();

            if (!$category->getIsActive() || !in_array($storeId, $category->getStoreIds())) {
                unset($storeUrls[$storeId]);
                continue;
            }

            if ($category->hasData() && ($currentCategory = $this->categoryFactory
                    ->create()
                    ->setStoreId($store->getId())
                    ->load($category->getEntityId()))
            ) {
                if ($requestPath = $this->getCategoryRewrite($currentCategory, (int)$storeId)) {
                    $currentCategory->setRequestPath($requestPath);
                }

                $storeBaseUrl       = $store->getBaseUrl();
                $currentCategoryUrl = $currentCategory->getUrl();
                //ned for situation like https://example.com/eu/ and https://example.com/
                $currentCategoryUrl = str_replace($currentBaseUrl, $storeBaseUrl, $currentCategoryUrl);
                // correct suffix for every store can't be added, because magento works incorrect,
                // maybe after magento fix (if need)
                if (strpos($currentCategoryUrl, $storeBaseUrl) === false) {
                    //create correct category way for every store, need if category use different path
                    $slashStoreBaseUrlCount     = substr_count($storeBaseUrl, '/');
                    $currentCategoryUrlExploded = explode('/', $currentCategoryUrl);
                    $currentCategoryUrl         = $storeBaseUrl . implode(
                        '/',
                        array_slice($currentCategoryUrlExploded, $slashStoreBaseUrlCount)
                    );
                }

                $urlAddition = $this->url->getUrlAddition($store);

                $preparedUrlAdditionCurrent = $this->getUrlAdditionalParsed(strstr($currentUrl, '?') ?: null);
                $preparedUrlAdditionStore   = $this->getUrlAdditionalParsed($urlAddition);
                $urlAdditionCategory        = $this->getPreparedUrlAdditional(
                    $preparedUrlAdditionCurrent,
                    $preparedUrlAdditionStore
                );

                if (
                    class_exists('\Mirasvit\SeoFilter\Model\ConfigProvider')
                    && $this->objectManager->get('\Mirasvit\SeoFilter\Model\ConfigProvider')->isEnabled()
                    && class_exists('\Mirasvit\SeoFilter\Service\FriendlyUrlService')
                ) {
                    $friendlyUrlService = $this->objectManager->get('\Mirasvit\SeoFilter\Service\FriendlyUrlService');
                    // active filters are retrieved inside function
                    $currentCategoryUrl = $friendlyUrlService->getUrl('', '', false, $currentCategoryUrl);
                }

                // if store use different attributes name will be added after use seo filter (if need)
                if ($this->alternateConfig->isHreflangCutCategoryAdditionalData()) {
                    $storeUrls[$storeId] = $currentCategoryUrl;
                } else {
                    $storeUrls[$storeId] = $currentCategoryUrl . $urlAdditionCategory;
                }
            }
        }

        if (count($storeUrls) === 1) {
            $storeUrls = []; // page doesn't have variations
        }

        //restore original store ID
        $this->categoryFactory->create()
            ->setStoreId($this->context->getStoreManager()->getStore()->getId());

        return $storeUrls;
    }

    protected function getCategoryRewrite(CategoryInterface $category, int $storeId): ?string
    {
        $urlRewrite = $this->urlRewriteFactory->create();
        $categoryRewrite = $urlRewrite->addFieldToFilter('entity_type', 'category')
            ->addFieldToFilter('redirect_type', 0)
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('entity_id', $category->getId())
            ->getFirstItem();

        return $categoryRewrite && $categoryRewrite->getId() ? $categoryRewrite->getRequestPath() : null;
    }

    /**
     * Parse additional url.
     */
    protected function getUrlAdditionalParsed(string $urlAddition = null): array
    {
        if (!$urlAddition) {
            return [];
        }
        $preparedUrlAddition = [];
        $urlAdditionParsed   = (substr($urlAddition, 0, 1) == '?') ? substr($urlAddition, 1) : $urlAddition;
        $urlAdditionParsed   = explode('&', $urlAdditionParsed);
        foreach ($urlAdditionParsed as $urlAdditionValue) {
            if (strpos($urlAdditionValue, '=') !== false) {
                $urlAdditionValueArray                          = explode('=', $urlAdditionValue);
                $preparedUrlAddition[$urlAdditionValueArray[0]] = $urlAdditionValueArray[1];
            } else {
                $preparedUrlAddition[$urlAdditionValue] = '';
            }
        }

        return $preparedUrlAddition;
    }

    /**
     * Prepare additional url.
     */
    protected function getPreparedUrlAdditional(array $preparedUrlAdditionCurrent, array $preparedUrlAdditionStore): string
    {
        $correctUrlAddition = [];
        $mergedUrlAddition  = array_merge_recursive($preparedUrlAdditionCurrent, $preparedUrlAdditionStore);
        foreach ($mergedUrlAddition as $keyUrlAddition => $valueUrlAddition) {
            if (is_array($valueUrlAddition) && $keyUrlAddition == '___store') {
                $correctUrlAddition[$keyUrlAddition] = $valueUrlAddition[1];
            } elseif (is_array($valueUrlAddition)) {
                $correctUrlAddition[$keyUrlAddition] = $valueUrlAddition[0];
            } elseif (array_key_exists($keyUrlAddition, $preparedUrlAdditionCurrent) || $keyUrlAddition == '___store') {
                $correctUrlAddition[$keyUrlAddition] = $valueUrlAddition;
            }
        }
        $urlAddition = (count($correctUrlAddition) > 0) ? $this->getUrlAdditionalString($correctUrlAddition) : '';

        return $urlAddition;
    }

    /**
     * Convert additional url array to string.
     */
    protected function getUrlAdditionalString(array $correctUrlAddition): string
    {
        $urlAddition      = '?';
        $urlAdditionArray = [];
        foreach ($correctUrlAddition as $keyUrlAddition => $valueUrlAddition) {
            $urlAdditionArray[] = $keyUrlAddition.'='.$valueUrlAddition;
        }
        $urlAddition .= implode('&', $urlAdditionArray);

        return $urlAddition;
    }
}
