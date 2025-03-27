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

namespace Mirasvit\SeoMarkup\Block\Rs;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoMarkup\Model\Config;
use Mirasvit\SeoMarkup\Model\Config\CategoryConfig;
use Mirasvit\SeoMarkup\Service\ProductRichSnippetsService;

class Category extends Template
{
    private $category;

    private $categoryConfig;

    private $productCollectionFactory;

    private $templateEngineService;

    private $registry;

    private $productSnippetService;

    public function __construct(
        CategoryConfig                 $categoryConfig,
        ProductCollectionFactory       $productCollectionFactory,
        TemplateEngineServiceInterface $templateEngineService,
        Registry                       $registry,
        Context                        $context,
        ProductRichSnippetsService     $productSnippetService
    ) {
        $this->categoryConfig           = $categoryConfig;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->templateEngineService    = $templateEngineService;
        $this->registry                 = $registry;
        $this->productSnippetService    = $productSnippetService;

        parent::__construct($context);
    }

    protected function _toHtml(): string
    {
        $data = $this->getJsonData();

        if (!$data) {
            return '';
        }

        return '<script type="application/ld+json">' . SerializeService::encode($data) . '</script>';
    }

    public function getJsonData(): ?array
    {
        $this->category = $this->registry->registry('current_category');

        if (!$this->category) {
            return null;
        }

        if ($this->category->getId() == $this->_storeManager->getStore()->getRootCategoryId()) {
            return null;
        }

        if (!$this->categoryConfig->isRsEnabled($this->getStoreId())) {
            return null;
        }

        $result[] = $this->getDataAsWebPage();

        return $result;
    }

    private function getDataAsWebPage(): array
    {
        $collection = $this->getCollection();
        $itemList   = [];

        if ($collection) {
            $itemList = $this->getItemList($collection);
        }

        $result = [
            '@context'   => Config::HTTP_SCHEMA_ORG,
            '@type'      => 'WebPage',
            'url'        => $this->_urlBuilder->escape($this->_urlBuilder->getCurrentUrl()),
            'mainEntity' => [
                '@type'           => 'offerCatalog',
                'name'            => $this->category->getName(),
                'url'             => $this->_urlBuilder->escape($this->_urlBuilder->getCurrentUrl()),
                'numberOfItems'   => $collection ? $collection->count() : '',
                'itemListElement' => $itemList,
            ],
        ];

        $image = $this->getImage($this->category);

        if (!empty($image)) {
            $result['mainEntity']['image'] = $image;
        }

        $description = $this->getDescription($this->category);

        if (!empty($description)) {
            $result['mainEntity']['description'] = $description;
        }

        return $result;
    }

    protected function getCollection(): ?AbstractCollection
    {
        $productOffersType = $this->categoryConfig->getProductOffersType($this->getStoreId());
        switch ($productOffersType) {
            case (CategoryConfig::PRODUCT_OFFERS_TYPE_DISABLED):
                return null;

            case (CategoryConfig::PRODUCT_OFFERS_TYPE_CURRENT_PAGE):
                $categoryProductsListBlock = $this->getLayout()->getBlock('category.products.list');

                if ($categoryProductsListBlock) {
                    $collection = $categoryProductsListBlock->getLoadedProductCollection();

                    $ids = [];

                    foreach ($collection as $product) {
                        $ids[] = $product->getId();
                    }

                    $collection = $this->productCollectionFactory->create();
                    $collection->addAttributeToSelect('*');
                    $collection->addAttributeToFilter(
                        'entity_id',
                        ['in' => $ids]
                    );
                    $collection->addFinalPrice();
                    $collection->load();
                } else {
                    return null;
                }
                break;

            case (CategoryConfig::PRODUCT_OFFERS_TYPE_CURRENT_CATEGORY):
                $collection = $this->productCollectionFactory->create();
                $collection->addAttributeToSelect('*');
                $collection->addCategoryFilter($this->category);
                $collection->addAttributeToFilter(
                    'visibility',
                    Visibility::VISIBILITY_BOTH
                );
                $collection->addAttributeToFilter(
                    'status',
                    Status::STATUS_ENABLED
                );
                $collection->addFinalPrice();
                $collection->load();
                break;
        }

        return $collection ?? null;
    }

    protected function getItemList(AbstractCollection $collection): array
    {
        $data = [];

        foreach ($collection as $product) {
            $data[] = $this->productSnippetService->getJsonData($product, true);
        }

        return $data;
    }

    private function getImage(\Magento\Catalog\Model\Category $category): string
    {
        $imageUrl = $category->getImageUrl();

        if ($this->categoryConfig->isImageEnabled($this->getStoreId()) && $imageUrl) {
            return $this->_urlBuilder->escape(
                $this->_storeManager->getStore()->getBaseUrl() . ltrim($imageUrl, '/')
            );
        }

        return '';
    }

    private function getDescription(\Magento\Catalog\Model\Category $category): string
    {
        $value          = '';
        $objectManager  = ObjectManager::getInstance();
        $contentService = $objectManager->get('Mirasvit\SeoContent\Service\ContentService');
        $content        = $contentService->getCurrentContent(TemplateInterface::RULE_TYPE_CATEGORY);

        switch ($this->categoryConfig->getDescriptionType($this->getStoreId())) {
            case CategoryConfig::DESCRIPTION_TYPE_DESCRIPTION:
                $description = $content->getData('category_description');
                $value       = !empty($description)
                    ? $description
                    : $this->templateEngineService->render('[category_description]', ['category' => $category]);
                break;
            case CategoryConfig::DESCRIPTION_TYPE_META:
                $description = $content->getData('meta_description');
                $value       = !empty($description)
                    ? $description
                    : $this->templateEngineService->render('[category_meta_description]', ['category' => $category]);
                break;
        }

        if (!empty($value)) {
            $value = preg_replace('/<style[^>]*>[\s\S]*?<\/style>/i', '', $value);
            $value = trim(strip_tags(str_replace('<', ' <', $value)));
            $value = preg_replace('/\s+/', ' ', $value);
        }

        return $value;
    }

    private function getStoreId(): int
    {
        if (!isset($this->storeId)) {
            try {
                $this->storeId = (int)$this->_storeManager->getStore()->getId();
            } catch (NoSuchEntityException $exception) {
                return Store::DEFAULT_STORE_ID;
            }
        }

        return $this->storeId;
    }
}
