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
 * @package   mirasvit/module-comment
 * @version   1.0.1
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Comment\Model;

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
        return $this->scopeConfig->isSetFlag(
            'mst_comment/general/is_enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isGuestCommentAllowed(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'mst_comment/general/allow_guest',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isAutoApproveComments(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'mst_comment/general/autoapprove',
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

    public function isAdminNotificationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag('mst_comment/general/is_admin_notifications_enabled');
    }
}
