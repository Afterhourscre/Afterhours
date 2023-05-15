<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Rule\Coupon;

use Aheadworks\Acr\Api\Data\CouponVariableInterface;
use Magento\Framework\DataObject;

/**
 * Class Variable
 * @package Aheadworks\Acr\Model\Rule\Coupon
 */
class Variable extends DataObject implements CouponVariableInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($couponCode)
    {
        return $this->setData(self::CODE, $couponCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationDate()
    {
        return $this->getData(self::EXPIRATION_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpirationDate($expirationDate)
    {
        return $this->setData(self::EXPIRATION_DATE, $expirationDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiscount()
    {
        return $this->getData(self::DISCOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscount($couponDiscount)
    {
        return $this->setData(self::DISCOUNT, $couponDiscount);
    }

    /**
     * {@inheritdoc}
     */
    public function getUsesPerCoupon()
    {
        return $this->getData(self::USES_PER_COUPON);
    }

    /**
     * {@inheritdoc}
     */
    public function setUsesPerCoupon($usesPerCoupon)
    {
        return $this->setData(self::USES_PER_COUPON, $usesPerCoupon);
    }
}
