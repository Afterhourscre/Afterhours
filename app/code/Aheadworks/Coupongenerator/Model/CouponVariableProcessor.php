<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model;

use Aheadworks\Coupongenerator\Api\CouponVariableProcessorInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class CouponVariableProcessor
 * @package Aheadworks\Coupongenerator\Model
 */
class CouponVariableProcessor implements CouponVariableProcessorInterface
{
    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var CouponVariableInterfaceFactory
     */
    private $couponVariableFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    private $localeCurrency;

    /**
     * @param StoreRepositoryInterface $storeRepository
     * @param RuleRepositoryInterface $ruleRepository
     * @param TimezoneInterface $timezone
     * @param CurrencyInterface $localeCurrency
     * @param CouponVariableInterfaceFactory $couponVariableFactory
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        RuleRepositoryInterface $ruleRepository,
        TimezoneInterface $timezone,
        CurrencyInterface $localeCurrency,
        CouponVariableInterfaceFactory $couponVariableFactory
    ) {
        $this->storeRepository = $storeRepository;
        $this->ruleRepository = $ruleRepository;
        $this->timezone = $timezone;
        $this->localeCurrency = $localeCurrency;
        $this->couponVariableFactory = $couponVariableFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponVariable($coupon, $storeId)
    {
        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        $store = $this->storeRepository->getById($storeId);

        /** @var \Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface $couponVariable */
        $couponVariable = $this->couponVariableFactory->create();

        $couponVariable
            ->setCouponCode($coupon->getCode())
            ->setCouponExpirationDate($this->getExpirationDate($coupon, $store))
            ->setCouponDiscount($this->getCouponDiscount($coupon, $store))
            ->setUsesPerCoupon($coupon->getUsageLimit())
        ;

        return $couponVariable;
    }

    /**
     * Convert coupon discount formatted for store
     *
     * @param \Magento\SalesRule\Api\Data\CouponInterface $coupon
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return string
     */
    private function getCouponDiscount($coupon, $store)
    {
        $currencyCode = $store->getCurrentCurrencyCode();
        $discount = 0;

        try {
            /** @var \Magento\SalesRule\Api\Data\RuleInterface $rule */
            $rule = $this->ruleRepository->getById($coupon->getRuleId());

            switch ($rule->getSimpleAction()) {
                case RuleInterface::DISCOUNT_ACTION_BY_PERCENT:
                    $discount = (number_format($rule->getDiscountAmount(), 2)+0).'%';
                    break;
                case RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT:
                case RuleInterface::DISCOUNT_ACTION_FIXED_AMOUNT_FOR_CART:
                    $discount = $this->convertToCurrency(
                        $currencyCode,
                        $rule->getDiscountAmount() * $store->getBaseCurrency()->getRate($currencyCode)
                    );
                    break;
                case RuleInterface::DISCOUNT_ACTION_BUY_X_GET_Y:
                    $discount = __(
                        "Buy %1 Get %2",
                        [$rule->getDiscountStep(), (number_format($rule->getDiscountAmount(), 2)+0)]
                    );
                    break;
            }
        } catch (NoSuchEntityException $e) {
        }

        return $discount;
    }

    /**
     * Get coupon expiration date formatted for store
     *
     * @param \Magento\SalesRule\Api\Data\CouponInterface $coupon
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return string
     */
    private function getExpirationDate($coupon, $store)
    {
        $expirationDate = '';
        if (!empty($coupon->getExpirationDate())) {
            $expirationDate = $this->timezone->formatDate(
                $coupon->getExpirationDate(),
                \IntlDateFormatter::LONG,
                true
            );
        }

        return $expirationDate;
    }

    /**
     * Convert amount to given currency and return string representation
     *
     * @param string $currencyCode
     * @param float $amount
     * @return string
     */
    private function convertToCurrency($currencyCode, $amount)
    {
        return $this->localeCurrency->getCurrency($currencyCode)->toCurrency(sprintf("%f", $amount));
    }
}
