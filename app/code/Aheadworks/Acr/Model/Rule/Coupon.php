<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Rule;

use Aheadworks\Acr\Api\Data\CouponRuleInterface;
use Aheadworks\Acr\Api\Data\CouponRuleExtensionInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Coupon
 * @package Aheadworks\Acr\Model\Rule
 */
class Coupon extends AbstractExtensibleObject implements CouponRuleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->_get(self::RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSalesRuleId()
    {
        return $this->_get(self::SALES_RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSalesRuleId($salesRuleId)
    {
        return $this->setData(self::SALES_RULE_ID, $salesRuleId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeLength()
    {
        return $this->_get(self::CODE_LENGTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeLength($codeLength)
    {
        return $this->setData(self::CODE_LENGTH, $codeLength);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeFormat()
    {
        return $this->_get(self::CODE_FORMAT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeFormat($codeFormat)
    {
        return $this->setData(self::CODE_FORMAT, $codeFormat);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodePrefix()
    {
        return $this->_get(self::CODE_PREFIX);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodePrefix($codePrefix)
    {
        return $this->setData(self::CODE_PREFIX, $codePrefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeSuffix()
    {
        return $this->_get(self::CODE_SUFFIX);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeSuffix($codeSuffix)
    {
        return $this->setData(self::CODE_SUFFIX, $codeSuffix);
    }

    /**
     * {@inheritdoc}
     */
    public function getCodeDash()
    {
        return $this->_get(self::CODE_DASH);
    }

    /**
     * {@inheritdoc}
     */
    public function setCodeDash($codeDash)
    {
        return $this->setData(self::CODE_DASH, $codeDash);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_get(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(CouponRuleExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
