<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface RuleInterface
 * @package Aheadworks\Acr\Api\Data
 */
interface RuleInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID                    = 'id';
    const NAME                  = 'name';
    const SUBJECT               = 'subject';
    const CONTENT               = 'content';
    const EMAIL_SEND_DAYS       = 'email_send_days';
    const EMAIL_SEND_HOURS      = 'email_send_hours';
    const EMAIL_SEND_MINUTES    = 'email_send_minutes';
    const COUPON_RULE           = 'coupon_rule';
    const STORE_IDS             = 'store_ids';
    const PRODUCT_TYPE_IDS      = 'product_type_ids';
    const CART_CONDITIONS       = 'cart_conditions';
    const PRODUCT_CONDITIONS    = 'product_conditions';
    const CUSTOMER_GROUPS       = 'customer_groups';
    const STATUS                = 'status';
    /**#@-*/

    /**
     * Get rule ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set rule ID
     *
     * @param int $ruleId
     * @return $this
     */
    public function setId($ruleId);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject();

    /**
     * Set subject
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject);

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Get email send days
     *
     * @return int
     */
    public function getEmailSendDays();

    /**
     * Set email send days
     *
     * @param int $days
     * @return $this
     */
    public function setEmailSendDays($days);

    /**
     * Get email send hours
     *
     * @return int
     */
    public function getEmailSendHours();

    /**
     * Set email send hours
     *
     * @param int $hours
     * @return $this
     */
    public function setEmailSendHours($hours);

    /**
     * Get email send minutes
     *
     * @return int
     */
    public function getEmailSendMinutes();

    /**
     * Set email send minutes
     *
     * @param int $minutes
     * @return $this
     */
    public function setEmailSendMinutes($minutes);

    /**
     * Get coupon rule data
     *
     * @return \Aheadworks\Acr\Api\Data\CouponRuleInterface|null
     */
    public function getCouponRule();

    /**
     * Set coupon rule data
     *
     * @param \Aheadworks\Acr\Api\Data\CouponRuleInterface $couponRule
     * @return $this
     */
    public function setCouponRule($couponRule);

    /**
     * Get store ids
     *
     * @return int[]
     */
    public function getStoreIds();

    /**
     * Set store ids
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds);

    /**
     * Get product type ids
     *
     * @return string[]
     */
    public function getProductTypeIds();

    /**
     * Set product type ids
     *
     * @param string[] $productTypeIds
     * @return $this
     */
    public function setProductTypeIds($productTypeIds);

    /**
     * Get cart conditions (serialized)
     *
     * @return string
     */
    public function getCartConditions();

    /**
     * Set cart conditions (serialized)
     *
     * @param string $cartConditions
     * @return $this
     */
    public function setCartConditions($cartConditions);

    /**
     * Get product conditions
     *
     * @return string
     */
    public function getProductConditions();

    /**
     * Set product conditions
     *
     * @param string $productConditions
     * @return $this
     */
    public function setProductConditions($productConditions);

    /**
     * Get customer groups
     *
     * @return string[]
     */
    public function getCustomerGroups();

    /**
     * Set customer groups
     *
     * @param string[] $customerGroups
     * @return $this
     */
    public function setCustomerGroups($customerGroups);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Acr\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Acr\Api\Data\RuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(RuleExtensionInterface $extensionAttributes);
}
