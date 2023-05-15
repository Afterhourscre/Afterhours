<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package Aheadworks\Coupongenerator\Model
 */
class Config
{
    /**
     * Location of the "Coupongenerator Email Sender" config param
     */
    const XML_PATH_SENDER_IDENTITY = 'aw_coupongenerator/general/email_sender';

    /**
     * Location of the "Coupongenerator Default Email Tempate" config param
     */
    const XML_PATH_TEMPLATE_IDENTITY = 'aw_coupongenerator/general/email_template';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get email sender
     *
     * @param null|int $websiteId
     * @return string
     */
    public function getEmailSender($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SENDER_IDENTITY,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get email sender name
     *
     * @param null|int $websiteId
     * @return string
     */
    public function getEmailSenderName($websiteId = null)
    {
        $sender = $this->getEmailSender($websiteId);

        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/name',
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get email template
     *
     * @param null|int $storeId
     * @return string
     */
    public function getEmailTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TEMPLATE_IDENTITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
