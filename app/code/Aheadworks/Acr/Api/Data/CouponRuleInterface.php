<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api\Data;

use Aheadworks\Acr\Api\Data\CouponRuleExtensionInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CouponRuleInterface
 * @package Aheadworks\Acr\Api\Data
 * @api
 */
interface CouponRuleInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const RULE_ID       = 'rule_id';
    const SALES_RULE_ID = 'sales_rule_id';
    const IS_ACTIVE     = 'is_active';
    const CODE_LENGTH   = 'code_length';
    const CODE_FORMAT   = 'code_format';
    const CODE_PREFIX   = 'code_prefix';
    const CODE_SUFFIX   = 'code_suffix';
    const CODE_DASH     = 'code_dash';
    /**#@-*/

    /**
     * Get rule id
     *
     * @return int
     */
    public function getRuleId();

    /**
     * Set rule id
     *
     * @param int $ruleId
     * @return $this
     */
    public function setRuleId($ruleId);

    /**
     * Get sales rule id
     *
     * @return int
     */
    public function getSalesRuleId();

    /**
     * Set sales rule id
     *
     * @param int $salesRuleId
     * @return $this
     */
    public function setSalesRuleId($salesRuleId);

    /**
     * Get is active
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get code length
     *
     * @return int
     */
    public function getCodeLength();

    /**
     * Set code length
     *
     * @param int $codeLength
     * @return $this
     */
    public function setCodeLength($codeLength);

    /**
     * Get code format
     *
     * @return string
     */
    public function getCodeFormat();

    /**
     * Set code format
     *
     * @param string $codeFormat
     * @return $this
     */
    public function setCodeFormat($codeFormat);

    /**
     * Get code prefix
     *
     * @return string
     */
    public function getCodePrefix();

    /**
     * Set code prefix
     *
     * @param string $codePrefix
     * @return $this
     */
    public function setCodePrefix($codePrefix);

    /**
     * Get code suffix
     *
     * @return string
     */
    public function getCodeSuffix();

    /**
     * Set code suffix
     *
     * @param string $codeSuffix
     * @return $this
     */
    public function setCodeSuffix($codeSuffix);

    /**
     * Get code dash
     *
     * @return string
     */
    public function getCodeDash();

    /**
     * Set code dash
     *
     * @param string $codeDash
     * @return $this
     */
    public function setCodeDash($codeDash);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Acr\Api\Data\CouponRuleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Acr\Api\Data\CouponRuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(CouponRuleExtensionInterface $extensionAttributes);
}
