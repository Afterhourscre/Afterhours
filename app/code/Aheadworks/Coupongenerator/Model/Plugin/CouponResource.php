<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Model\ResourceModel\Coupon as SalesruleCouponResource;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTime as Date;
use Aheadworks\Coupongenerator\Model\SalesruleRepository;

/**
 * Class CouponResource
 * @package Aheadworks\Coupongenerator\Model\Plugin
 * @codeCoverageIgnore
 */
class CouponResource
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Date
     */
    private $date;

    /**
     * @var \Aheadworks\Coupongenerator\Model\SalesruleRepository
     */
    private $salesruleRepository;

    /**
     * @param DateTime $dateTime
     * @param Date $date
     * @param SalesruleRepository $salesruleRepository
     */
    public function __construct(
        DateTime $dateTime,
        Date $date,
        SalesruleRepository $salesruleRepository
    ) {
        $this->dateTime = $dateTime;
        $this->date = $date;
        $this->salesruleRepository = $salesruleRepository;
    }

    /**
     * Set valid coupon expiration date for new coupon
     *
     * @param SalesruleCouponResource $subject
     * @param AbstractModel $coupon
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave($subject, $coupon)
    {
        if ($coupon->getId()) {
            return [$coupon];
        }

        try {
            /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruledDataObject */
            $salesruledDataObject = $this->salesruleRepository->getByRuleId($coupon->getRuleId());

            $expirationDate = null;
            $expirationDays = $salesruledDataObject->getExpirationDays();
            $nowTimestamp = $this->dateTime->formatDate($this->date->gmtTimestamp());

            if ($expirationDays) {
                $expirationDate = (new \DateTime($nowTimestamp))
                ->modify('+'. $expirationDays .' days')->format('Y-m-d H:i:s');
            }

            $coupon->setExpirationDate($expirationDate);
        } catch (NoSuchEntityException $e) {
        }

        return [$coupon];
    }

    /**
     * Prevent to update coupongenerator's coupons
     *
     * @param SalesruleCouponResource $subject
     * @param \Closure $proceed
     * @param \Magento\SalesRule\Model\Rule $rule
     * @return SalesruleCouponResource
     */
    public function aroundUpdateSpecificCoupons($subject, \Closure $proceed, $rule)
    {
        if ($rule && $rule->getId() && $rule->hasDataChanges()) {
            try {
                /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruledDataObject */
                $salesruledDataObject = $this->salesruleRepository->getByRuleId($rule->getId());
                if ($salesruledDataObject->getId()) {
                    return $subject;
                }
            } catch (NoSuchEntityException $e) {
            }
        }
        $result = $proceed($rule);

        return $result;
    }
}
