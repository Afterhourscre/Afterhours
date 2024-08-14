<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\RuleExtensionInterface;
use Aheadworks\Acr\Api\Data\CouponRuleInterface;
use Aheadworks\Acr\Model\ResourceModel\Rule as RuleResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Rule
 * @package Aheadworks\Acr\Model
 */
class Rule extends AbstractModel implements RuleInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(RuleResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($ruleId)
    {
        return $this->setData(self::ID, $ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->getData(self::SUBJECT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        return $this->setData(self::SUBJECT, $subject);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailSendDays()
    {
        return $this->getData(self::EMAIL_SEND_DAYS);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailSendDays($days)
    {
        return $this->setData(self::EMAIL_SEND_DAYS, $days);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailSendHours()
    {
        return $this->getData(self::EMAIL_SEND_HOURS);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailSendHours($hours)
    {
        return $this->setData(self::EMAIL_SEND_HOURS, $hours);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailSendMinutes()
    {
        return $this->getData(self::EMAIL_SEND_MINUTES);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailSendMinutes($minutes)
    {
        return $this->setData(self::EMAIL_SEND_MINUTES, $minutes);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponRule()
    {
        return $this->getData(self::COUPON_RULE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponRule($couponRule)
    {
        return $this->setData(self::COUPON_RULE, $couponRule);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        return $this->getData(self::STORE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds($storeIds)
    {
        return $this->setData(self::STORE_IDS, $storeIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductTypeIds()
    {
        return $this->getData(self::PRODUCT_TYPE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductTypeIds($productTypeIds)
    {
        return $this->setData(self::PRODUCT_TYPE_IDS, $productTypeIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getCartConditions()
    {
        return $this->getData(self::CART_CONDITIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCartConditions($cartConditions)
    {
        return $this->setData(self::CART_CONDITIONS, $cartConditions);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductConditions()
    {
        return $this->getData(self::PRODUCT_CONDITIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductConditions($productConditions)
    {
        return $this->setData(self::PRODUCT_CONDITIONS, $productConditions);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroups()
    {
        return $this->getData(self::CUSTOMER_GROUPS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroups($customerGroups)
    {
        return $this->setData(self::CUSTOMER_GROUPS, $customerGroups);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(RuleExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
