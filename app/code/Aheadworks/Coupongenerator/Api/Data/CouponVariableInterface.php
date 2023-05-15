<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Api\Data;

/**
 * Interface CouponVariableInterface
 * @api
 */
interface CouponVariableInterface
{
    /**#@+
     * Constants defined for keys of the data array
     * Identical to the name of the getter in snake case
     */
    const COUPON_CODE               = 'coupon_code';
    const COUPON_EXPIRATION_DATE    = 'coupon_expiration_date';
    const COUPON_DISCOUNT           = 'coupon_discount';
    const USES_PER_COUPON           = 'uses_per_coupon';
    /**#@-*/

    /**
     * Get coupon code
     *
     * @return string
     */
    public function getCouponCode();

    /**
     * Set coupon code
     *
     * @param string $couponCode
     * @return $this
     */
    public function setCouponCode($couponCode);

    /**
     * Get coupon expiration date
     *
     * @return string
     */
    public function getCouponExpirationDate();

    /**
     * Set coupon expiration date
     *
     * @param string $expirationDate
     * @return $this
     */
    public function setCouponExpirationDate($expirationDate);

    /**
     * Get coupon discount
     *
     * @return string
     */
    public function getCouponDiscount();

    /**
     * Set coupon discount
     *
     * @param string $couponDiscount
     * @return $this
     */
    public function setCouponDiscount($couponDiscount);

    /**
     * Get uses per coupon
     *
     * @return string
     */
    public function getUsesPerCoupon();

    /**
     * Set uses per coupon
     *
     * @param string $usesPerCoupon
     * @return $this
     */
    public function setUsesPerCoupon($usesPerCoupon);
}
