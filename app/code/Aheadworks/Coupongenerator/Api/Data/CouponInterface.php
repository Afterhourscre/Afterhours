<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CouponInterface
 * @api
 */
interface CouponInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array
     * Identical to the name of the getter in snake case
     */
    const ID                    = 'id';
    const COUPON_ID             = 'coupon_id';
    const IS_DEACTIVATED        = 'is_deactivated';
    const ADMIN_USER_ID         = 'admin_user_id';
    const RECIPIENT_EMAIL       = 'recipient_email';
    const CUSTOMER_ID           = 'customer_id';
    /**#@-*/

    /**
     * Get coupon id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set coupon id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get Magento coupon id
     *
     * @return int|null
     */
    public function getCouponId();

    /**
     * Set Magento coupon id
     *
     * @param int $couponId
     * @return $this
     */
    public function setCouponId($couponId);

    /**
     * Get coupon deactivation state
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsDeactivated();

    /**
     * Set coupon deactivation state
     *
     * @param bool $isDeactivated
     * @return $this
     */
    public function setIsDeactivated($isDeactivated);

    /**
     * Get admin user id
     *
     * @return int
     */
    public function getAdminUserId();

    /**
     * Set admin user id
     *
     * @param int $adminUserId
     * @return $this
     */
    public function setAdminUserId($adminUserId);

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getRecipientEmail();

    /**
     * Set recipient email
     *
     * @param string $recipientEmail
     * @return $this
     */
    public function setRecipientEmail($recipientEmail);

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int $customerId|null
     * @return mixed
     */
    public function setCustomerId($customerId);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Coupongenerator\Api\Data\CouponExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Coupongenerator\Api\Data\CouponExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Coupongenerator\Api\Data\CouponExtensionInterface $extensionAttributes
    );
}
