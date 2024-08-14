<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Api\Data\CouponVariableInterface;
use Aheadworks\Acr\Api\Data\CouponVariableInterfaceFactory;
use Aheadworks\Acr\Model\Rule\Coupon\Processor as CouponProcessor;
use Aheadworks\Acr\Api\CouponVariableManagementInterface;
use Aheadworks\Acr\Model\Rule\Coupon\Generator as CouponGenerator;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Api\Data\CouponRuleInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;

/**
 * Class CouponVariableManagement
 * @package Aheadworks\Acr\Model
 */
class CouponVariableManagement implements CouponVariableManagementInterface
{
    /**
     * @var CouponGenerator
     */
    private $couponGenerator;

    /**
     * @var CouponProcessor
     */
    private $couponProcessor;

    /**
     * @var CouponVariableInterfaceFactory
     */
    private $couponVariableFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var CouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @param CouponGenerator $couponGenerator
     * @param CouponProcessor $couponProcessor
     * @param CouponVariableInterfaceFactory $couponVariableFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param CouponRepositoryInterface $couponRepository
     */
    public function __construct(
        CouponGenerator $couponGenerator,
        CouponProcessor $couponProcessor,
        CouponVariableInterfaceFactory $couponVariableFactory,
        RuleRepositoryInterface $ruleRepository,
        CouponRepositoryInterface $couponRepository
    ) {
        $this->couponGenerator = $couponGenerator;
        $this->couponProcessor = $couponProcessor;
        $this->couponVariableFactory = $couponVariableFactory;
        $this->ruleRepository = $ruleRepository;
        $this->couponRepository = $couponRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponVariable($ruleId, $storeId)
    {
        /** @var CouponVariableInterface $couponVariable */
        $couponVariable = $this->couponVariableFactory->create();
        try {
            $rule = $this->ruleRepository->get($ruleId);

            /** @var CouponRuleInterface|null $couponRule */
            $couponRule = $rule->getCouponRule();
            if ($couponRule && $couponRule->getIsActive()) {
                /** @var CouponInterface $coupon */
                $coupon = $this->couponGenerator->getCoupon($couponRule);
                if ($coupon) {
                    $couponVariable = $this->couponProcessor->getCouponVariable($coupon, $storeId);
                }
            }
        } catch (NoSuchEntityException $e) {
            // do nothing
        }
        return $couponVariable;
    }

    /**
     * {@inheritdoc}
     */
    public function getTestCouponVariable()
    {
        /** @var CouponVariableInterface $couponVariable */
        $couponVariable = $this->couponVariableFactory->create();

        $couponVariable
            ->setCode('TEST')
            ->setDiscount('50%')
            ->setExpirationDate('Jan 20, 2020')
            ->setUsesPerCoupon(1);

        return $couponVariable;
    }
}
