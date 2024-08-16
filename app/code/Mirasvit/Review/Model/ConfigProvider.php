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
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Model;


use \IntlDateFormatter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'catalog/review/active',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isGuestsAllowed(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'catalog/review/allow_guest',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getDateFormat(int $store = null): string
    {
        $default = IntlDateFormatter::MEDIUM . '_0';
        $formats = [
            IntlDateFormatter::MEDIUM . '_0', IntlDateFormatter::MEDIUM . '_1',
            IntlDateFormatter::FULL . '_0', IntlDateFormatter::FULL . '_1',
            IntlDateFormatter::LONG . '_0', IntlDateFormatter::LONG . '_1',
            IntlDateFormatter::SHORT . '_0', IntlDateFormatter::SHORT . '_1',
        ];

        $value = (string)$this->scopeConfig->getValue(
            'mst_comment/general/date_format',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return in_array($value, $formats) ? $value : $default;
    }

    public function getDateFormatValue(int $store = null): int
    {
        $format = $store ? $this->getDateFormat($store) : $this->getDateFormat();
        $parts  = explode('_', $format);

        return (int)$parts[0];
    }

    public function getDateFormatIsShowTime(int $store = null): bool
    {
        $format = $store ? $this->getDateFormat($store) : $this->getDateFormat();
        $parts  = explode('_', $format);

        return (int)$parts[1] === 1;
    }

    public function displayCountry(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_review/general/display_country',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getDefaultSorting(): string
    {
        return (string)$this->scopeConfig->getValue(
            'mst_review/general/default_sorting',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isRecommendEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_review/general/recommend_enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getRecommendLabel(): string
    {
        $label = $this->scopeConfig->getValue(
            'mst_review/general/recommend_label',
            ScopeInterface::SCOPE_STORE
        );

        return $label ? (string)$label : __("I recommend this product");
    }

    public function displayProsAndCons(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_review/general/display_pros_cons',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function displayVerifiedBuyer(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_review/general/display_verified_buyer',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function displayProductOption(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_review/general/display_product_option',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getOrderStatusesForVerifiedBuyer(): array
    {
        $statuses = (string)$this->scopeConfig->getValue('mst_review/general/order_status');

        return !$statuses ? ['complete'] : explode(',', $statuses);
    }

    public function isMediaEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_review/media/is_media_enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function displayReviewPhotoGallery(): bool
    {
        return $this->isMediaEnabled()
            && (bool)$this->scopeConfig->getValue(
            'mst_review/media/display_review_photo_gallery',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getMaxNumberFiles(): int
    {
        $maxNumFiles = (int)$this->scopeConfig->getValue(
            'mst_review/media/max_number_files',
            ScopeInterface::SCOPE_STORE
        );

        return $maxNumFiles ?: 5;
    }

    public function isAggregationEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_review/ai/is_aggregate',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isShowInfoHint(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_review/ai/show_hint',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiKey(): string
    {
        return (string)$this->scopeConfig->getValue(
            'mst_review/ai/api_key',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiModel(): string
    {
        return 'gpt-3.5-turbo';
    }

    public function getCountReviewLimit(): int
    {
        return 5;
    }

    public function getMaxReviewCountToAggregate(): int
    {
        return 10;
    }

    public function getLenghtLimit(): int
    {
        return 4096;
    }

    public function isAutowriteEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'mst_review/ai/is_autowrite',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getLanguageByStore(int $storeId): string
    {
        return \Locale::getDisplayLanguage($this->scopeConfig->getValue(
            'general/locale/code',
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }

    public function isAdminNotificationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag('mst_review/notifications/is_admin_notifications_enabled');
    }

    public function isEmailNotificationsForPendingReviewsEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag('mst_review/notifications/is_email_notifications_enabled');
    }

    public function getPendingReviewNotificationEmails(): ?array
    {
        $emails = (string)$this->scopeConfig->getValue('mst_review/notifications/emails');

        return trim($emails)
            ? array_filter(array_map('trim', explode(',', $emails)))
            : null;
    }

    public function getSender(): array
    {
        return [
            'name'  => $this->scopeConfig->getValue('trans_email/ident_general/name'),
            'email' => $this->scopeConfig->getValue('trans_email/ident_general/email'),
        ];
    }
}
