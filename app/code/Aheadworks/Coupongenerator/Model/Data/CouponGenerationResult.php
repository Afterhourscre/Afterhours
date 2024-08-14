<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Data;

use Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * CouponGenerationResult data model
 *
 * @codeCoverageIgnore
 */
class CouponGenerationResult extends AbstractSimpleObject implements CouponGenerationResultInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCoupon()
    {
        return $this->_get(self::COUPON);
    }

    /**
     * {@inheritdoc}
     */
    public function setCoupon($coupon)
    {
        return $this->setData(self::COUPON, $coupon);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->_get(self::MESSAGES);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessages($messages)
    {
        return $this->setData(self::MESSAGES, $messages);
    }
}
