<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model;

use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * FAQ config model
 */
class Config
{
    /**
     * Default number of columns to display config path
     */
    const XML_PATH_NUMBER_OF_COLUMNS = 'faq/general/number_of_columns';

    /**
     * Default number of columns to display config path
     */
    const XML_PATH_FAQ_SEARCH_ENABLED = 'faq/general/faq_search_enabled';

    /**
     * Display link to FAQ in the main navigation config path
     */
    const XML_PATH_NAVIGATION_MENU_LINK_ENABLED = 'faq/general/navigation_menu_link_enabled';

    /**
     * FAQ name path
     */
    const XML_PATH_FAQ_NAME = 'faq/general/faq_name';

    /**
     * Customer groups who have not access to FAQ
     */
    const XML_PATH_FAQ_GROUPS = 'faq/general/groups_with_disabled_faq';

    /**
     * FAQ route config path
     */
    const XML_PATH_FAQ_ROUTE = 'faq/general/faq_route';

    /**
     * FAQ enable question form
     */
    const XML_PATH_FAQ_ENABLE_QUESTION = 'faq/general/enable_ask_queston_form';

    /**
     * FAQ email for the questions
     */
    const XML_PATH_FAQ_QUESTION_EMAIL = 'faq/general/question_email';

    /**
     * FAQ meta title config path
     */
    const XML_PATH_FAQ_META_TITLE = 'faq/general/meta_title';

    /**
     * FAQ meta description config path
     */
    const XML_PATH_FAQ_META_DESCRIPTION = 'faq/general/meta_description';

    /**
     * Default customer groups to display helpfulness in Articles config path
     */
    const XML_PATH_HELPFULNESS_CUSTOMER_GROUPS = 'faq/helpfulness/helpfulness_customer_groups';

    /**
     * Show FAQ helpfulness rate before voting in Articles config path
     */
    const XML_PATH_HELPFULNESS_RATE_BEFORE_VOTING_ENABLED = 'faq/helpfulness/helpfulness_rate_before_voting';

    /**
     * Show FAQ helpfulness rate after voting in Articles config path
     */
    const XML_PATH_HELPFULNESS_RATE_AFTER_VOTING_ENABLED = 'faq/helpfulness/helpfulness_rate_after_voting';

    /**
     * Configuration path to faq sitemap change frequency
     */
    const XML_PATH_SITEMAP_CHANGEFREQ = 'sitemap/aw_faq/changefreq';

    /**
     * Configuration path to faq sitemap priority
     */
    const XML_PATH_SITEMAP_PRIORITY = 'sitemap/aw_faq/priority';

    /**
     * Default email template for question email
     */
    const DEFAULT_EMAIL_TEMPLATE = 'aw_faq_email_template';

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Customer session
     *
     * @var Session
     */
    private $session;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $session
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Session $session,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->session = $session;
        $this->storeManager = $storeManager;
    }

    /**
     * Get faq display column count on FAQ homepage
     *
     * @param null|string|bool|int|Store $store
     * @return int
     */
    public function getDefaultNumberOfColumnsToDisplay($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NUMBER_OF_COLUMNS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get faq Storefront Name
     *
     * @return string
     */
    public function getFaqName()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_NAME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get faq route
     *
     * @param int $storeId
     * @return string
     * @internal param StoreManagerInterface $store
     * @internal param WebsiteInterface $website
     * @internal param StoreInterface $store
     */
    public function getFaqRoute($storeId = null)
    {
        $websiteCode = null;

        if ($storeId) {
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
            $websiteCode = $this->storeManager->getWebsite($websiteId)->getCode();
        }

        return $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_ROUTE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteCode
        );
    }

    /**
     * Get faq Meta title
     *
     * @return string
     */
    public function getFaqMetaTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_META_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get faq Meta description
     *
     * @return string
     */
    public function getFaqMetaDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_META_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks if FAQ search is enabled
     *
     * @return bool
     */
    public function isFaqSearchEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_FAQ_SEARCH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks if FAQ link in Categories is enabled
     *
     * @return bool
     */
    public function isNavigationMenuLinkEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NAVIGATION_MENU_LINK_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get customer groups for FAQ Article helpfulness
     *
     * @return array
     */
    public function getDefaultCustomerGroupsToDisplayHelpfulness()
    {
        $settingsValue = $this->scopeConfig->getValue(
            self::XML_PATH_HELPFULNESS_CUSTOMER_GROUPS,
            ScopeInterface::SCOPE_STORE
        );

        return explode(',', $settingsValue);
    }

    /**
     * Checks if FAQ helpfulness rate before voting in Articles enabled
     *
     * @return bool
     */
    public function isHelpfulnessRateBeforeVotingEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HELPFULNESS_RATE_BEFORE_VOTING_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks if FAQ helpfulness rate after voting in Articles enabled
     *
     * @return bool
     */
    public function isHelpfulnessRateAfterVotingEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_HELPFULNESS_RATE_AFTER_VOTING_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get customer groups who can view FAQ content
     *
     * @return array|bool
     */
    public function getFaqGroups()
    {
        $settingsValue = $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_GROUPS,
            ScopeInterface::SCOPE_STORE
        );

        return $settingsValue ? explode(',', $settingsValue) : false;
    }

    /**
     * Check disabling FAQ for current user
     *
     * @return bool
     */
    public function isDisabledFaqForCurrentCustomer()
    {
        $groups = $this->getFaqGroups();
        $groupId = (string)$this->session->getCustomerGroupId();
        if (!$groups) {
            return false;
        }
        return !(in_array($groupId, $groups) || in_array(Group::CUST_GROUP_ALL, $groups));
    }

    /**
     * Get faq change frequency
     *
     * @param int $storeId
     * @return string
     */
    public function getSitemapChangeFrequency($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEMAP_CHANGEFREQ,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get faq priority
     *
     * @param int $storeId
     * @return string
     */
    public function getSitemapPriority($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEMAP_PRIORITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get is enabled question form
     *
     * @return bool
     */
    public function getIsEnableQuestionForm()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_FAQ_ENABLE_QUESTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get email for the questions
     *
     * @return string
     */
    public function getQuestionEmail()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FAQ_QUESTION_EMAIL,
            ScopeInterface::SCOPE_STORE
        );
    }
}
