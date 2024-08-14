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

namespace Mirasvit\SeoMarkup\Model\Config;

use Magento\Store\Model\ScopeInterface;
use Mirasvit\SeoMarkup\Model\Config;

class ProductConfig extends Config
{
    const DESCRIPTION_TYPE_DESCRIPTION       = 1;
    const DESCRIPTION_TYPE_META              = 2;
    const DESCRIPTION_TYPE_SHORT_DESCRIPTION = 3;

    const WEIGHT_UNIT_KG = 'KGM';
    const WEIGHT_UNIT_LB = 'LBR';
    const WEIGHT_UNIT_G  = 'GRM';

    const ITEM_CONDITION_MANUAL  = 1;
    const ITEM_CONDITION_NEW_ALL = 2;

    public function isRsEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/product/is_rs_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isRemoveNativeRs(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/product/is_remove_native_rs',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isPriceEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/product/is_price_enabled',
            ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }

    public function getDescriptionType(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/seo_markup/product/description_type',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isImageEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/product/is_image_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isAvailabilityEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/product/is_availability_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isAcceptedPaymentMethodEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/product/is_accepted_payment_method_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isAvailableDeliveryMethodEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/product/is_available_delivery_method_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isCategoryEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/product/is_category_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isMpnEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/product/is_mpn_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getBrandAttribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/brand_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getModelAttribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/model_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getManufacturerPartNumber(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/mpn_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getColorAttribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/color_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getSizeAttribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/size_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getWeightUnitType(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/weight_unit_type',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isDimensionsEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/product/is_dimensions_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getDimensionUnit(?int $storeId = null): string
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/product/dimension_unit',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function getDimensionHeightAttribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/dimension_height_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getDimensionWidthAttribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/dimension_width_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }


    public function getDimensionDepthAttribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/dimension_depth_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getItemConditionType(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/item_condition_type',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getItemConditionAttribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/item_condition_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getItemConditionAttributeValueNew(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/item_condition_attribute_value_new',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getItemConditionAttributeValueUsed(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/item_condition_attribute_value_used',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getItemConditionAttributeValueRefurbished(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/item_condition_attribute_value_refurbished',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getItemConditionAttributeValueDamaged(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/item_condition_attribute_value_damaged',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isIndividualReviewsEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag('seo/seo_markup/product/is_individual_reviews_enabled');
    }

    public function isProductVariantsEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/product/is_variants_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getGtin8Attribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/gtin8_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getGtin12Attribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/gtin12_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getGtin13Attribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/gtin13_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getGtin14Attribute(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/product/gtin14_attribute',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
