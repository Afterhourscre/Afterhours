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

class CategoryConfig extends Config
{
    const DESCRIPTION_TYPE_DESCRIPTION = 1;
    const DESCRIPTION_TYPE_META        = 2;

    const PRODUCT_OFFERS_TYPE_DISABLED         = 0;
    const PRODUCT_OFFERS_TYPE_CURRENT_PAGE     = 1;
    const PRODUCT_OFFERS_TYPE_CURRENT_CATEGORY = 2;

    public function isRemoveNativeRs(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/category/is_remove_native_rs',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isRsEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/category/is_rs_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isOgEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/category/is_og_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getProductOffersType(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/seo_markup/category/product_offers_type',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isCategoryRatingEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/category/is_category_rating_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getDefaultPageSize(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            'catalog/frontend/grid_per_page',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getDescriptionType(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            'seo/seo_markup/category/description_type',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isImageEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/category/is_image_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
