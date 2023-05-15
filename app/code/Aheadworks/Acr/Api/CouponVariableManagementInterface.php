<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Api;

/**
 * Interface CouponVariableManagementInterface
 * @package Aheadworks\Acr\Api
 */
interface CouponVariableManagementInterface
{
    /**
     * Get coupon variable
     *
     * @param int $ruleId
     * @param int $storeId
     * @return \Aheadworks\Acr\Api\Data\CouponVariableInterface
     */
    public function getCouponVariable($ruleId, $storeId);

    /**
     * Get test coupon variable
     *
     * @return \Aheadworks\Acr\Api\Data\CouponVariableInterface
     */
    public function getTestCouponVariable();
}
