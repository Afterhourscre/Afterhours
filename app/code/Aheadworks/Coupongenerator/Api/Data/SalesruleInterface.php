<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SalesruleInterface
 * @api
 */
interface SalesruleInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array
     * Identical to the name of the getter in snake case
     */
    const ID                    = 'id';
    const RULE_ID               = 'rule_id';
    const EXPIRATION_DAYS       = 'expiration_days';
    const COUPON_LENGTH         = 'coupon_length';
    const CODE_FORMAT           = 'code_format';
    const CODE_PREFIX           = 'code_prefix';
    const CODE_SUFFIX           = 'code_suffix';
    const CODE_DASH             = 'code_dash';
    /**#@-*/

    /**
     * Get rule id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set rule id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get Magento rule id
     *
     * @return int|null
     */
    public function getRuleId();

    /**
     * Set Magento rule id
     *
     * @param int $ruleId
     * @return $this
     */
    public function setRuleId($ruleId);

    /**
     * Get coupon expiration time (days)
     *
     * @return int
     */
    public function getExpirationDays();

    /**
     * Set coupon expiration time (days)
     *
     * @param int $days
     * @return $this
     */
    public function setExpirationDays($days);

    /**
     * Get coupon length (excluding prefix, suffix, and separators)
     *
     * @return int
     */
    public function getCouponLength();

    /**
     * Set coupon length (excluding prefix, suffix, and separators)
     *
     * @param int $length
     * @return $this
     */
    public function setCouponLength($length);

    /**
     * Get coupon code format
     *
     * @return string
     */
    public function getCodeFormat();

    /**
     * Set coupon code format
     *
     * @param string $format
     * @return $this
     */
    public function setCodeFormat($format);

    /**
     * Get coupon code prefix
     *
     * @return string|null
     */
    public function getCodePrefix();

    /**
     * Set coupon code prefix
     *
     * @param string $prefix|null
     * @return $this
     */
    public function setCodePrefix($prefix = null);

    /**
     * Get coupon code suffix
     *
     * @return string|null
     */
    public function getCodeSuffix();

    /**
     * Set coupon code suffix
     *
     * @param string $suffix|null
     * @return $this
     */
    public function setCodeSuffix($suffix = null);

    /**
     * Get coupon code dash
     *
     * @return string|null
     */
    public function getCodeDash();

    /**
     * Set coupon code dash
     *
     * @param string $dash|null
     * @return $this
     */
    public function setCodeDash($dash = null);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Coupongenerator\Api\Data\SalesruleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Coupongenerator\Api\Data\SalesruleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Coupongenerator\Api\Data\SalesruleExtensionInterface $extensionAttributes
    );
}
