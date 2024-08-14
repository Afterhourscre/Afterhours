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


namespace Mirasvit\SeoAi\Model;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Module\Manager;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    const CURRENT_OPENAI_MODEL = 'gpt-3.5-turbo';

    private $scopeConfig;

    private $encryptor;

    private $moduleManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        Manager $moduleManager
    ) {
        $this->scopeConfig   = $scopeConfig;
        $this->encryptor     = $encryptor;
        $this->moduleManager = $moduleManager;
    }

    public function isHelperEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo_ai/general/is_enabled');
    }

    public function isUpdateRewrites(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo_audit/ai_helper/is_update');
    }

    public function isIncludeStoreData(): bool
    {
        return (bool)$this->scopeConfig->getValue('seo_audit/ai_helper/is_include_store');
    }

    public function getApiKey(): ?string
    {
        $key = $this->scopeConfig->getValue('seo_ai/general/openai_key');

        return $key ? $this->encryptor->decrypt($key) : null;
    }

    public function getModel(): string
    {
        return self::CURRENT_OPENAI_MODEL;
    }

    public function getCantRunReasons(): array
    {
        $reasons = [];

        if (!$this->moduleManager->isEnabled('Mirasvit_SeoAudit')) {
            $reasons[] = 'Mirasvit_SeoAudit module disabled.';
        }

        if (!$this->moduleManager->isEnabled('Mirasvit_SeoContent')) {
            $reasons[] = 'Mirasvit_SeoContent module disabled.';
        }

        if (!$this->isHelperEnabled()) {
            $reasons[] = 'SEO AI Helper disabled.';
        }

        if (!$this->scopeConfig->getValue('seo_audit/ai_helper/is_auto_fix_meta')) {
            $reasons[] = 'The feature is disable in the configurations of the Mirasvit_SeoAudit module.';
        }

        if (!$this->getApiKey()) {
            $reasons[] = 'OpenAI Secret Key is not set.';
        }

        return $reasons;
    }

    public function getAdditionalStoreData(): string
    {
        return trim((string)$this->scopeConfig->getValue(
            'seo_audit/ai_helper/store_description',
            ScopeInterface::SCOPE_STORE
        ));
    }

    public function isCronEnabled(): bool
    {
        return $this->isHelperEnabled() && $this->isUpdateRewrites()
            && (bool)$this->scopeConfig->getValue('seo_audit/ai_helper/is_cron');
    }

    public function getLanguage(int $storeId): string
    {
        return \Locale::getDisplayLanguage($this->scopeConfig->getValue(
            'general/locale/code',
            ScopeInterface::SCOPE_STORES,
            $storeId
        ));
    }
}
