<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Api;

use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface;

/**
 * Interface CouponVariableProcessorInterface
 * @api
 */
interface CouponVariableProcessorInterface
{
    /**
     * Get coupon variable from specified coupon
     *
     * @param \Magento\SalesRule\Api\Data\CouponInterface $coupon
     * @param int $storeId
     * @return CouponVariableInterface
     */
    public function getCouponVariable($coupon, $storeId);
}
