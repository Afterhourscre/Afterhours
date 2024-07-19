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

class OrganizationConfig extends Config
{
    /**
     * @var array
     */
    private $socialLinkConfigs
        = [
            'seo/seo_markup/organization/youtube_link',
            'seo/seo_markup/organization/facebook_link',
            'seo/seo_markup/organization/linkedin_link',
            'seo/seo_markup/organization/instagram_link',
            'seo/seo_markup/organization/pinterest_link',
            'seo/seo_markup/organization/tumblr_link',
            'seo/seo_markup/organization/twitter_link',
        ];

    public function isRsEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/organization/is_rs_enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isCustomName(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/organization/is_custom_name',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getCustomName(?int $storeId = null): string
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_name',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }


    public function isCustomAddressCountry(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/organization/is_custom_address_country',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getCustomAddressCountry(?int $storeId = null): string
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_address_country',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function isCustomAddressLocality(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/organization/is_custom_address_locality',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getCustomAddressLocality(?int $storeId = null): string
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/address_locality',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function isCustomAddressRegion(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/organization/is_custom_address_region',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getCustomAddressRegion(?int $storeId = null): string
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_address_region',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function isCustomPostalCode(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/organization/is_custom_postal_code',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getCustomPostalCode(?int $storeId = null): string
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_postal_code',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function isCustomStreetAddress(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/organization/is_custom_street_address',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getCustomStreetAddress(?int $storeId = null): string
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_street_address',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function isCustomTelephone(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/organization/is_custom_telephone',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getCustomTelephone(?int $storeId = null): string
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_telephone',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function getCustomFaxNumber(?int $storeId = null): string
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_fax_number',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function isCustomEmail(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            'seo/seo_markup/organization/is_custom_email',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getCustomEmail(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            'seo/seo_markup/organization/custom_email',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getSocialLinks(?int $storeId = null): array
    {
        $socialLinks = [];
        foreach ($this->socialLinkConfigs as $socialLinkConfig) {
            $socialLink = $this->scopeConfig->getValue(
                $socialLinkConfig,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );

            if (isset($socialLink)) {
                $socialLinks[] = $socialLink;
            }
        }

        return $socialLinks;
    }
}
