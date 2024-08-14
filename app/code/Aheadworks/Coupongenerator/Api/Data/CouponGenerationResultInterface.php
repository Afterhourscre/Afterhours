<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Api\Data;

/**
 * Interface CouponGenerationResultInterface
 * @api
 */
interface CouponGenerationResultInterface
{
    /**#@+
     * Constants defined for keys of the data array
     * Identical to the name of the getter in snake case
     */
    const COUPON        = 'coupon';
    const MESSAGES      = 'messages';
    /**#@-*/

    /**
     * Get generated coupon
     *
     * @return \Magento\SalesRule\Api\Data\CouponInterface|null
     */
    public function getCoupon();

    /**
     * Set generated coupon
     *
     * @param \Magento\SalesRule\Api\Data\CouponInterface|null $coupon
     * @return $this
     */
    public function setCoupon($coupon);

    /**
     * Get coupon generation messages
     *
     * @return array
     */
    public function getMessages();

    /**
     * Set coupon generation messages
     *
     * @param array $messages
     * @return $this
     */
    public function setMessages($messages);
}
