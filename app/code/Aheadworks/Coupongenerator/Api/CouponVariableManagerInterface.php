<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Api;

/**
 * Interface CouponVariableManagerInterface
 * @api
 */
interface CouponVariableManagerInterface
{
    /**
     * Generate new coupon
     *
     * @param int|null $ruleId
     * @param string|null $alias
     * @return void
     */
    public function generateCoupon($ruleId = null, $alias = null);

    /**
     * Get coupon code for specified coupon, it will be generated if is not exist
     *
     * @param string|null $alias
     * @return string|null
     */
    public function getCouponCode($alias = null);

    /**
     * Get coupon expiration date for specified coupon
     *
     * @param string|null $alias
     * @return string|null
     */
    public function getCouponExpirationDate($alias = null);

    /**
     * Get coupon discount for specified coupon
     *
     * @param string|null $alias
     * @return string|null
     */
    public function getCouponDiscount($alias = null);

    /**
     * Get uses per coupon for specified coupon
     *
     * @param string|null $alias
     * @return string|null
     */
    public function getUsesPerCoupon($alias = null);

    /**
     * Set recipient of generated coupons by email
     *
     * @param string $recipientEmail
     * @return $this
     */
    public function setRecipientByEmail($recipientEmail);

    /**
     * Set recipient of generated coupons by customer Id
     *
     * @param int $customerId
     * @return $this
     */
    public function setRecipientByCustomerId($customerId);

    /**
     * Set recipient store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);
}
