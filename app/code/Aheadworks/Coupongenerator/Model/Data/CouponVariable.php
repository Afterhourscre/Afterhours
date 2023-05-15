<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Data;

use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * CouponVariable data model
 *
 * @codeCoverageIgnore
 */
class CouponVariable extends AbstractSimpleObject implements CouponVariableInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCouponCode()
    {
        return $this->_get(self::COUPON_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponCode($couponCode)
    {
        return $this->setData(self::COUPON_CODE, $couponCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponExpirationDate()
    {
        return $this->_get(self::COUPON_EXPIRATION_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponExpirationDate($expirationDate)
    {
        return $this->setData(self::COUPON_EXPIRATION_DATE, $expirationDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponDiscount()
    {
        return $this->_get(self::COUPON_DISCOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponDiscount($couponDiscount)
    {
        return $this->setData(self::COUPON_DISCOUNT, $couponDiscount);
    }

    /**
     * {@inheritdoc}
     */
    public function getUsesPerCoupon()
    {
        return $this->_get(self::USES_PER_COUPON);
    }

    /**
     * {@inheritdoc}
     */
    public function setUsesPerCoupon($usesPerCoupon)
    {
        return $this->setData(self::USES_PER_COUPON, $usesPerCoupon);
    }
}
