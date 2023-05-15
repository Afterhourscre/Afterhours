<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Data;

use Aheadworks\Coupongenerator\Api\Data\SalesruleInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Salesrule data model
 *
 * @codeCoverageIgnore
 */
class Salesrule extends AbstractExtensibleObject implements SalesruleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

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
    public function getExpirationDays()
    {
        return $this->_get(self::EXPIRATION_DAYS);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpirationDays($days)
    {
        return $this->setData(self::EXPIRATION_DAYS, $days);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponLength()
    {
        return $this->_get(self::COUPON_LENGTH);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponLength($length)
    {
        return $this->setData(self::COUPON_LENGTH, $length);
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
    public function setCodeFormat($format)
    {
        return $this->setData(self::CODE_FORMAT, $format);
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
    public function setCodePrefix($prefix = null)
    {
        return $this->setData(self::CODE_PREFIX, $prefix);
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
    public function setCodeSuffix($suffix = null)
    {
        return $this->setData(self::CODE_SUFFIX, $suffix);
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
    public function setCodeDash($dash = null)
    {
        return $this->setData(self::CODE_DASH, $dash);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Aheadworks\Coupongenerator\Api\Data\SalesruleExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \Aheadworks\Coupongenerator\Api\Data\SalesruleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Coupongenerator\Api\Data\SalesruleExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
