<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model;

use Aheadworks\Coupongenerator\Api\CouponVariableManagerInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface;
use Magento\Framework\DataObject;

/**
 * Class AbstractCouponVariableManager
 * @package Aheadworks\Coupongenerator\Model
 */
abstract class AbstractCouponVariableManager extends DataObject implements CouponVariableManagerInterface
{
    /**
     * @var CouponVariableInterface[]
     */
    protected $couponsData = [];

    /**
     * @param array $data
     */
    public function __construct(
        array $data = []
    ) {
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function generateCoupon($ruleId = null, $alias = null);

    /**
     * {@inheritdoc}
     */
    public function getCouponCode($alias = null)
    {
        /** @var \Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface $couponVariable */
        $couponVariable = $this->getCouponVariableByAlias($alias);

        if ($couponVariable) {
            return $couponVariable->getCouponCode();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponExpirationDate($alias = null)
    {
        /** @var \Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface $couponVariable */
        $couponVariable = $this->getCouponVariableByAlias($alias);

        if ($couponVariable) {
            return $couponVariable->getCouponExpirationDate();
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponDiscount($alias = null)
    {
        /** @var \Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface $couponVariable */
        $couponVariable = $this->getCouponVariableByAlias($alias);

        if ($couponVariable) {
            return $couponVariable->getCouponDiscount();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsesPerCoupon($alias = null)
    {
        /** @var \Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface $couponVariable */
        $couponVariable = $this->getCouponVariableByAlias($alias);

        if ($couponVariable) {
            return $couponVariable->getUsesPerCoupon();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function setRecipientByEmail($recipientEmail);

    /**
     * {@inheritdoc}
     */
    abstract public function setRecipientByCustomerId($customerId);

    /**
     * {@inheritdoc}
     */
    abstract public function setStoreId($storeId);

    /**
     * Parse coupon alias
     *
     * @param string|null $alias
     * @return string|null
     */
    protected function parseCouponAlias($alias)
    {
        if ($alias) {
            return (string)trim($alias);
        }

        return $alias;
    }

    /**
     * Get coupon variable by alias, if alias is not specified last variable will be returned
     *
     * @param string|null $alias
     * @return bool|CouponVariableInterface
     */
    private function getCouponVariableByAlias($alias)
    {
        $couponAlias = $this->parseCouponAlias($alias);
        $couponVariable = false;

        if ($couponAlias) {
            if (isset($this->couponsData[$couponAlias])) {
                /** @var \Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface $couponVariable */
                $couponVariable = $this->couponsData[$couponAlias];
            }
        } else {
            if (count($this->couponsData) > 0) {
                $keys = array_keys($this->couponsData);
                $last = end($keys);
                $couponVariable = $this->couponsData[$last];
            }
        }

        return $couponVariable;
    }
}
