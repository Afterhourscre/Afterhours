<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Api;

/**
 * Interface CouponManagerInterface
 * @api
 */
interface CouponManagerInterface
{
    /**
     * Generate and send coupon for email
     *
     * @param int $ruleId
     * @param string $email
     * @param bool $isSendEmail
     * @return \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface result
     * @throws \Magento\Framework\Exception\NoSuchEntityException If rule with the specified ID does not exist
     */
    public function generateForEmail($ruleId, $email, $isSendEmail = true);

    /**
     * Generate and send coupon for customer
     *
     * @param int $ruleId
     * @param int $customerId
     * @param bool $isSendEmail
     * @return \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface result
     * @throws \Magento\Framework\Exception\LocalizedException If customer is not valid the specified rule
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer or rule does not exist
     */
    public function generateForCustomer($ruleId, $customerId, $isSendEmail = true);
}
