<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Model\Flag;
use Aheadworks\Acr\Model\FlagFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;

/**
 * Class Config
 * @package Aheadworks\Acr\Model
 */
class Config
{
    /**
     * Configuration path to enable module parameter
     */
    const XML_PATH_MODULE_OUTPUT_DISABLED = 'advanced/modules_disable_output/Aheadworks_Acr';

    /**
     * Configuration path to sender
     */
    const XML_PATH_SENDER = 'aw_acr/general/sender';

    /**
     * Configuration path to test email recipient
     */
    const XML_PATH_TEST_EMAIL_RECIPIENT = 'aw_acr/general/test_email_recipient';

    /**
     * Configuration path to enable test mode
     */
    const XML_PATH_ENABLE_TEST_MODE = 'aw_acr/general/enable_test_mode';

    /**
     * Configuration path to mail log keep emails for
     */
    const XML_PATH_MAIL_LOG_KEEP_FOR = 'aw_acr/mail_log/keep_for';

    /**
     * Process cart history last exec time
     */
    const PROCESS_CART_HISTORY_LAST_EXEC_TIME = 'aw_acr_process_cart_history_last_exec_time';

    /**
     * Send emails last exec time
     */
    const SEND_EMAILS_LAST_EXEC_TIME = 'aw_acr_send_emails_last_exec_time';

    /**
     * Clear log last exec time
     */
    const CLEAR_LOG_LAST_EXEC_TIME = 'aw_acr_clear_log_last_exec_time';

    /**
     * Configuration path to header template
     */
    const XML_PATH_HEADER_TEMPLATE = 'design/email/header_template';

    /**
     * Configuration path to footer template
     */
    const XML_PATH_FOOTER_TEMPLATE = 'design/email/footer_template';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    /**
     * @var Flag
     */
    private $flag;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SenderResolverInterface $senderResolver
     * @param FlagFactory $flagFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SenderResolverInterface $senderResolver,
        FlagFactory $flagFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->senderResolver = $senderResolver;
        $this->flag = $flagFactory->create();
    }

    /**
     * Is module output enabled
     *
     * @param null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return !$this->scopeConfig->isSetFlag(
            self::XML_PATH_MODULE_OUTPUT_DISABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get sender
     * @param int|null $storeId
     * @return string
     */
    public function getSender($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get sender email
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSenderEmail($storeId = null)
    {
        $sender = $this->getSender($storeId);
        $data = $this->senderResolver->resolve($sender, $storeId);

        return $data['email'];
    }

    /**
     * Get sender name
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSenderName($storeId = null)
    {
        $sender = $this->getSender($storeId);
        $data = $this->senderResolver->resolve($sender, $storeId);

        return $data['name'];
    }

    /**
     * Get test recipient email
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTestEmailRecipient($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TEST_EMAIL_RECIPIENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if test mode is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isTestModeEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_TEST_MODE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get keep emails for
     *
     * @return string
     */
    public function getKeepEmailsFor()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_MAIL_LOG_KEEP_FOR
        );
    }

    /**
     * Get process cart history last exec time
     *
     * @return int
     */
    public function getProcessCartHistoryLastExecTime()
    {
        return (int)$this->getFlagData(self::PROCESS_CART_HISTORY_LAST_EXEC_TIME);
    }

    /**
     * Set process cart history last exec time
     *
     * @param int $timestamp
     * @return $this
     */
    public function setProcessCartHistoryLastExecTime($timestamp)
    {
        $this->setFlagData(self::PROCESS_CART_HISTORY_LAST_EXEC_TIME, $timestamp);
        return $this;
    }

    /**
     * Get send emails last exec time
     *
     * @return int
     */
    public function getSendEmailsLastExecTime()
    {
        return (int)$this->getFlagData(self::SEND_EMAILS_LAST_EXEC_TIME);
    }

    /**
     * Set send emails last exec time
     *
     * @param int $timestamp
     * @return $this
     */
    public function setSendEmailsLastExecTime($timestamp)
    {
        $this->setFlagData(self::SEND_EMAILS_LAST_EXEC_TIME, $timestamp);
        return $this;
    }

    /**
     * Get clear log last exec time
     *
     * @return int
     */
    public function getClearLogLastExecTime()
    {
        return (int)$this->getFlagData(self::CLEAR_LOG_LAST_EXEC_TIME);
    }

    /**
     * Set clear log last exec time
     *
     * @param int $timestamp
     * @return $this
     */
    public function setClearLogLastExecTime($timestamp)
    {
        $this->setFlagData(self::CLEAR_LOG_LAST_EXEC_TIME, $timestamp);
        return $this;
    }

    /**
     * Get flag data
     *
     * @param string $param
     * @return array
     */
    private function getFlagData($param)
    {
        $this->flag
            ->unsetData()
            ->setAcrFlagCode($param)
            ->loadSelf();

        return $this->flag->getFlagData();
    }

    /**
     * Set flag data
     *
     * @param string $param
     * @param mixed $value
     * @return $this
     */
    private function setFlagData($param, $value)
    {
        $this->flag
            ->unsetData()
            ->setAcrFlagCode($param)
            ->loadSelf()
            ->setFlagData($value)
            ->save();

        return $this;
    }

    /**
     * Get header template id
     *
     * @return int|string
     */
    public function getHeaderTemplateId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HEADER_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get footer template id
     *
     * @return int|string
     */
    public function getFooterTemplateId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FOOTER_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
