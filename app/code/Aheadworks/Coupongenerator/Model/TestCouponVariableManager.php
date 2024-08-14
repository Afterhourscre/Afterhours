<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model;

use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterfaceFactory;

/**
 * Class TestCouponVariableManager
 * @package Aheadworks\Coupongenerator\Model
 */
class TestCouponVariableManager extends AbstractCouponVariableManager
{
    /**#@+
     * Test coupon data
     */
    const TEST_CODE               = 'CODE';
    const TEST_EXPIRATION_DATE    = 'EXPIRATION-DATE';
    const TEST_DISCOUNT           = 'DISCOUNT';
    const TEST_USES_PER_COUPON    = 'USES-PER-COUPON';
    const NO_ALIAS_PREFIX         = 'TEST';
    /**#@-*/

    /**
     * @var CouponVariableInterfaceFactory
     */
    private $couponVariableFactory;

    /**
     * @param CouponVariableInterfaceFactory $couponVariableFactory
     * @param array $data
     */
    public function __construct(
        CouponVariableInterfaceFactory $couponVariableFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->couponVariableFactory = $couponVariableFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function generateCoupon($ruleId = null, $alias = null)
    {
        $couponAlias = $this->parseCouponAlias($alias);

        /** @var CouponVariableInterface $couponVariable */
        $couponVariable = $this->couponVariableFactory->create();

        if ($couponAlias) {
            $couponVariable
                ->setCouponCode($couponAlias . '-' . self::TEST_CODE)
                ->setCouponDiscount($couponAlias . '-' . self::TEST_DISCOUNT)
                ->setCouponExpirationDate($couponAlias . '-' . self::TEST_EXPIRATION_DATE)
                ->setUsesPerCoupon($couponAlias . '-' . self::TEST_USES_PER_COUPON);
            $this->couponsData[$couponAlias] = $couponVariable;
        } else {
            $couponVariable
                ->setCouponCode(self::NO_ALIAS_PREFIX . '-' . self::TEST_CODE)
                ->setCouponDiscount(self::NO_ALIAS_PREFIX . '-' . self::TEST_DISCOUNT)
                ->setCouponExpirationDate(self::NO_ALIAS_PREFIX . '-' . self::TEST_EXPIRATION_DATE)
                ->setUsesPerCoupon(self::NO_ALIAS_PREFIX . '-' . self::TEST_USES_PER_COUPON);
            $this->couponsData[] = $couponVariable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientByEmail($recipientEmail)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientByCustomerId($customerId)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this;
    }
}
