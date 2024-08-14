<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Data;

use Aheadworks\Coupongenerator\Api\Data\CouponInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Coupon data model
 *
 * @codeCoverageIgnore
 */
class Coupon extends AbstractExtensibleObject implements CouponInterface
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
    public function getCouponId()
    {
        return $this->_get(self::COUPON_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponId($couponId)
    {
        return $this->setData(self::COUPON_ID, $couponId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDeactivated()
    {
        return $this->_get(self::IS_DEACTIVATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDeactivated($isDeactivated)
    {
        return $this->setData(self::IS_DEACTIVATED, $isDeactivated);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminUserId()
    {
        return $this->_get(self::ADMIN_USER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdminUserId($adminUserId)
    {
        return $this->setData(self::ADMIN_USER_ID, $adminUserId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientEmail()
    {
        return $this->_get(self::RECIPIENT_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientEmail($recipientEmail)
    {
        return $this->setData(self::RECIPIENT_EMAIL, $recipientEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Aheadworks\Coupongenerator\Api\Data\CouponExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \Aheadworks\Coupongenerator\Api\Data\CouponExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Coupongenerator\Api\Data\CouponExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
