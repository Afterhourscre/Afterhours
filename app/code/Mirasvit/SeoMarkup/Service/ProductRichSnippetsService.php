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

namespace Mirasvit\SeoMarkup\Service;

use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Store\Model\Store;
use Mirasvit\Seo\Api\Service\TemplateEngineServiceInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Service\ContentService;
use Mirasvit\SeoMarkup\Api\Data\ExtenderInterface;
use Mirasvit\SeoMarkup\Block\Rs\Product\AggregateOfferData;
use Mirasvit\SeoMarkup\Block\Rs\Product\OfferData;
use Mirasvit\SeoMarkup\Block\Rs\Product\RatingData;
use Mirasvit\SeoMarkup\Block\Rs\Product\ReviewData;
use Mirasvit\SeoMarkup\Model\Config;
use Mirasvit\SeoMarkup\Model\Config\ProductConfig;
use Mirasvit\SeoMarkup\Repository\ExtenderRepository;

class ProductRichSnippetsService extends Template
{
    private const SCHEMA_PROPERTY_COLOR = 'color';
    private const SCHEMA_PROPERTY_SIZE  = 'size';

    private const SCHEMA_TYPE_PRODUCT       = 'Product';
    private const SCHEMA_TYPE_PRODUCT_GROUP = 'ProductGroup';

    private $productConfig;

    private $templateEngineService;

    private $offerData;

    private $aggregateOfferData;

    private $reviewData;

    private $ratingData;

    private $imageHelper;

    private $contentService;

    private $extenderRepository;

    private $snippetService;

    /**
     * @var ?int
     */
    private $storeId;

    public function __construct(
        ProductConfig                  $productConfig,
        TemplateEngineServiceInterface $templateEngineService,
        OfferData                      $offerData,
        AggregateOfferData             $aggregateOfferData,
        ReviewData                     $reviewData,
        RatingData                     $ratingData,
        ImageHelper                    $imageHelper,
        ContentService                 $contentService,
        ExtenderRepository             $extenderRepository,
        SnippetService                 $snippetService,
        Context                        $context
    ) {
        $this->productConfig         = $productConfig;
        $this->templateEngineService = $templateEngineService;
        $this->offerData             = $offerData;
        $this->aggregateOfferData    = $aggregateOfferData;
        $this->reviewData            = $reviewData;
        $this->ratingData            = $ratingData;
        $this->imageHelper           = $imageHelper;
        $this->contentService        = $contentService;
        $this->extenderRepository    = $extenderRepository;
        $this->snippetService        = $snippetService;

        parent::__construct($context);
    }

    /**
     * @return array|false
     */
    public function getJsonData(?ProductInterface $product, bool $dry = false)
    {
        if (!$product) {
            return false;
        }

        $product = $dry ? $product : $product->load($product->getId());

        /** @var Store $store */
        $store = $this->_storeManager->getStore();

        $extendedTypes = [Configurable::TYPE_CODE, BundleType::TYPE_CODE, Grouped::TYPE_CODE];
        if ($dry === false && in_array($product->getTypeId(), $extendedTypes)) {
            $offer = $this->aggregateOfferData->getData($product, $store);
        } else {
            $offer = $this->offerData->getData($product, $store, $dry);
        }

        $isProductGroup = $this->productConfig->isProductVariantsEnabled($this->getStoreId())
            && ($product->getTypeId() === Configurable::TYPE_CODE);

        $sku = $this->templateEngineService->render('[product_sku]', ['product' => $product]);

        $values = [
            '@context'        => Config::HTTP_SCHEMA_ORG,
            '@type'           => $isProductGroup ? self::SCHEMA_TYPE_PRODUCT_GROUP : self::SCHEMA_TYPE_PRODUCT,
            'name'            => $this->templateEngineService->render('[product_name]', ['product' => $product]),
            'sku'             => $sku,
            'mpn'             => $this->getManufacturerPartNumber($product),
            'image'           => $this->getImage($product),
            'category'        => $this->getCategoryName($product),
            'brand'           => $this->getBrand($product),
            'model'           => $this->getModel($product),
            'color'           => $this->getColor($product),
            'weight'          => $this->getWeight($product),
            'width'           => $this->getDimensionValue('width', $product),
            'height'          => $this->getDimensionValue('height', $product),
            'depth'           => $this->getDimensionValue('depth', $product),
            'description'     => $this->getDescription($product),
            'gtin8'           => $this->getGtinValue(8, $product),
            'gtin12'          => $this->getGtinValue(12, $product),
            'gtin13'          => $this->getGtinValue(13, $product),
            'gtin14'          => $this->getGtinValue(14, $product),
            'offers'          => $offer,
            'review'          => $this->reviewData->getData($product, $store),
            'aggregateRating' => $this->ratingData->getData($product, $store),
        ];

        if ($isProductGroup) {
            $values['productGroupID'] = $sku;
            $values['variesBy']       = $this->getVariesBy($product);
            $values['hasVariant']     = $this->getVariants($product);
        }

        $extenders = $this->extenderRepository
            ->getListForProduct($product, ExtenderInterface::PRODUCT_TYPE, $this->getStoreId());
        foreach ($extenders as $extender) {
            $values = $this->snippetService
                ->extendRichSnippet($values, $extender->getSnippetArray(), $extender->isOverrideEnabled());
        }

        return array_filter($values);
    }

    /**
     * @return string|false
     */
    private function getManufacturerPartNumber(ProductInterface $product)
    {
        $storeId = $this->getStoreId();
        if (
            $this->productConfig->isMpnEnabled($storeId)
            && $attribute = $this->productConfig->getManufacturerPartNumber($storeId)
        ) {
            return $this->templateEngineService->render("[product_$attribute]", ['product' => $product]);
        }

        return false;
    }

    /**
     * @param Product $product
     *
     * @return string|false
     */
    private function getImage(ProductInterface $product)
    {
        if ($this->productConfig->isImageEnabled($this->getStoreId())) {
            return $this->imageHelper->init($product, 'product_page_image_large')->getUrl();
        }

        return false;
    }

    /**
     * @return string|false
     */
    private function getCategoryName(ProductInterface $product)
    {
        if (!$this->productConfig->isCategoryEnabled($this->getStoreId())) {
            return false;
        }

        return $this->templateEngineService->render('[product_category_name]', ['product' => $product]);
    }

    /**
     * @return array|false
     */
    private function getBrand(ProductInterface $product)
    {
        if ($attribute = $this->productConfig->getBrandAttribute($this->getStoreId())) {
            if ($brand = $this->templateEngineService->render("[product_$attribute]", ['product' => $product])) {
                return [
                    '@type' => 'Brand',
                    'name'  => $brand,
                ];
            }
        }

        return false;
    }

    /**
     * @return string|false
     */
    private function getModel(ProductInterface $product)
    {
        if ($attribute = $this->productConfig->getModelAttribute($this->getStoreId())) {
            return $this->templateEngineService->render("[product_$attribute]", ['product' => $product]);
        }

        return false;
    }

    /**
     * @return string|false
     */
    private function getColor(ProductInterface $product)
    {
        if ($attribute = $this->productConfig->getColorAttribute($this->getStoreId())) {
            return $this->templateEngineService->render("[product_$attribute]", ['product' => $product]);
        }

        return false;
    }

    /**
     * @return array|false
     */
    private function getWeight(ProductInterface $product)
    {
        $unitCode = $this->productConfig->getWeightUnitType($this->getStoreId());

        if (!$unitCode) {
            return false;
        }

        $value = $this->templateEngineService->render('[product_weight]', ['product' => $product]);

        if (!$value) {
            return false;
        }
        $value = number_format((float)$value, 4);

        return [
            '@type'    => 'QuantitativeValue',
            'value'    => $value,
            'unitCode' => $unitCode,
        ];
    }

    /**
     * @return false|string
     */
    private function getDescription(ProductInterface $product)
    {
        $content = $this->contentService->getCurrentContent(TemplateInterface::RULE_TYPE_PRODUCT, $product);

        switch ($this->productConfig->getDescriptionType($this->getStoreId())) {
            case ProductConfig::DESCRIPTION_TYPE_DESCRIPTION:
                $description = $content->getData('full_description');
                $value       = !empty($description)
                    ? $description
                    : $this->templateEngineService->render('[product_description]', ['product' => $product]);
                break;
            case ProductConfig::DESCRIPTION_TYPE_META:
                $description = $content->getData('meta_description');
                $value       = !empty($description)
                    ? $description
                    : $this->templateEngineService->render('[page_meta_description]', ['product' => $product]);
                break;
            case ProductConfig::DESCRIPTION_TYPE_SHORT_DESCRIPTION:
                $description = $content->getData('short_description');
                $value       = !empty($description)
                    ? $description
                    : $this->templateEngineService->render('[product_short_description]', ['product' => $product]);
                break;
            default:
                $value = $product->getShortDescription();
                break;
        }

        return $value ? strip_tags($value) : false;
    }

    /**
     * @return array|false
     */
    private function getDimensionValue(string $type, ProductInterface $product)
    {
        if (!$this->productConfig->isDimensionsEnabled($this->getStoreId())) {
            return false;
        }

        $unitCode = $this->productConfig->getDimensionUnit($this->getStoreId());

        if (!$unitCode) {
            return false;
        }

        switch ($type) {
            case 'width':
                $attribute = $this->productConfig->getDimensionWidthAttribute($this->getStoreId());
                break;

            case 'height':
                $attribute = $this->productConfig->getDimensionHeightAttribute($this->getStoreId());
                break;

            case 'depth':
                $attribute = $this->productConfig->getDimensionDepthAttribute($this->getStoreId());
                break;

            default:
                $attribute = false;
        }

        if (!$attribute) {
            return false;
        }

        $value = $this->templateEngineService->render("[product_$attribute]", ['product' => $product]);

        if (!$value) {
            return false;
        }

        return [
            '@type'    => 'QuantitativeValue',
            'value'    => $value,
            'unitCode' => $unitCode,
        ];
    }

    /**
     * @return false|string
     */
    private function getGtinValue(int $number, ProductInterface $product)
    {
        switch ($number) {
            case 8:
                $attribute = $this->productConfig->getGtin8Attribute($this->getStoreId());
                break;

            case 12:
                $attribute = $this->productConfig->getGtin12Attribute($this->getStoreId());
                break;

            case 13:
                $attribute = $this->productConfig->getGtin13Attribute($this->getStoreId());
                break;

            case 14:
                $attribute = $this->productConfig->getGtin14Attribute($this->getStoreId());
                break;

            default:
                $attribute = false;
        }

        if (!$attribute) {
            return false;
        }

        return $this->templateEngineService->render("[product_$attribute]", ['product' => $product]);
    }

    private function getVariesBy(ProductInterface $product): array
    {
        $variesBy = [];
        foreach ($this->getConfigAttributes($product) as $attribute) {
            $schemaOrgProperties = $this->getSchemaOrgProperties();
            $variesBy[]          = isset($schemaOrgProperties[$attribute])
                ? Config::HTTP_SCHEMA_ORG . '/' . $schemaOrgProperties[$attribute]
                : $attribute;
        }

        return $variesBy;
    }

    private function getVariants(ProductInterface $product): array
    {
        $variants         = [];
        $configAttributes = $this->getConfigAttributes($product);
        $child            = $product->getTypeInstance()->getUsedProductCollection($product)
            ->addAttributeToSelect(ProductInterface::VISIBILITY)
            ->addPriceData();

        foreach ($child as $item) {
            $jsonData = $this->getJsonData($item, true);

            foreach ($configAttributes as $attributeCode) {
                $value = $item->getAttributeText($attributeCode);
                if (!isset($jsonData[$attributeCode]) && $value) {
                    $jsonData[$attributeCode] = $value;
                }
            }

            $variants[] = $jsonData;
        }

        return $variants;
    }

    private function getConfigAttributes(ProductInterface $product): array
    {
        $options    = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
        $attributes = [];
        foreach ($options as $option) {
            $attributes[] = $option[AttributeInterface::ATTRIBUTE_CODE];
        }

        return $attributes;
    }

    private function getSchemaOrgProperties(): array
    {
        $configAttributes = array_filter(
            [
                self::SCHEMA_PROPERTY_COLOR => $this->productConfig->getColorAttribute($this->getStoreId()),
                self::SCHEMA_PROPERTY_SIZE  => $this->productConfig->getSizeAttribute($this->getStoreId()),
            ]
        );

        return array_flip($configAttributes);
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
