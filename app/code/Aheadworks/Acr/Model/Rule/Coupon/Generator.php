<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Rule\Coupon;

use Aheadworks\Acr\Api\Data\CouponRuleInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterface;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterfaceFactory;
use Magento\SalesRule\Api\CouponManagementInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Generator
 * @package Aheadworks\Acr\Model\Rule\Coupon
 */
class Generator
{
    /**
     * @var CouponGenerationSpecInterfaceFactory
     */
    private $couponGenerationSpecFactory;

    /**
     * @var CouponManagementInterface
     */
    private $couponManagement;

    /**
     * @var CouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param CouponGenerationSpecInterfaceFactory $couponGenerationSpecFactory
     * @param CouponManagementInterface $couponManagement
     * @param CouponRepositoryInterface $couponRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CouponGenerationSpecInterfaceFactory $couponGenerationSpecFactory,
        CouponManagementInterface $couponManagement,
        CouponRepositoryInterface $couponRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->couponGenerationSpecFactory = $couponGenerationSpecFactory;
        $this->couponManagement = $couponManagement;
        $this->couponRepository = $couponRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Generate coupon
     *
     * @param CouponRuleInterface $couponRule
     * @return CouponInterface|false
     */
    public function getCoupon(CouponRuleInterface $couponRule)
    {
        /** @var CouponGenerationSpecInterface $couponGenerationSpec */
        $couponGenerationSpec = $this->couponGenerationSpecFactory->create();
        $couponGenerationSpec
            ->setRuleId($couponRule->getSalesRuleId())
            ->setQuantity(1)
            ->setLength($couponRule->getCodeLength())
            ->setFormat($couponRule->getCodeFormat())
            ->setPrefix($couponRule->getCodePrefix())
            ->setSuffix($couponRule->getCodeSuffix())
            ->setDelimiterAtEvery($couponRule->getCodeDash());
        $result = $this->couponManagement->generate($couponGenerationSpec);
        $code = array_shift($result);

        $this->searchCriteriaBuilder
            ->addFilter('code', $code);
        $couponsList = $this->couponRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems()
        ;
        foreach ($couponsList as $couponData) {
            if (is_array($couponData)) {
                /** @var CouponInterface $coupon */
                $coupon = $this->couponRepository->getById($couponData['coupon_id']);
            } else {
                $coupon = $couponData;
            }
            return $coupon;
        }
        return false;
    }
}
