<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Coupon;

use Aheadworks\Coupongenerator\Api\Data\CouponInterfaceFactory;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterfaceFactory;
use Magento\SalesRule\Api\CouponManagementInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Coupongenerator\Model\SalesruleRepository;
use Magento\SalesRule\Api\Data\CouponExtensionFactory;

/**
 * Class Generator
 * @package Aheadworks\Coupongenerator\Model\Coupon
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Generator
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SalesruleRepository
     */
    private $salesruleRepository;

    /**
     * @var CouponInterfaceFactory
     */
    private $couponInterfaceFactory;

    /**
     * @var CouponGenerationSpecInterfaceFactory
     */
    private $couponGenerationSpecInterfaceFactory;

    /**
     * @var CouponManagementInterface
     */
    private $couponManagement;

    /**
     * @var CouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @var \Magento\SalesRule\Api\Data\CouponExtensionFactory
     */
    private $magentoCouponExtensionFactory;

    /**
     * @param EntityManager $entityManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SalesruleRepository $salesruleRepository
     * @param CouponInterfaceFactory $couponInterfaceFactory
     * @param CouponGenerationSpecInterfaceFactory $couponGenerationSpecInterfaceFactory
     * @param CouponManagementInterface $couponManagement
     * @param CouponRepositoryInterface $couponRepository
     * @param CouponExtensionFactory $magentoCouponExtensionFactory
     */
    public function __construct(
        EntityManager $entityManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SalesruleRepository $salesruleRepository,
        CouponInterfaceFactory $couponInterfaceFactory,
        CouponGenerationSpecInterfaceFactory $couponGenerationSpecInterfaceFactory,
        CouponManagementInterface $couponManagement,
        CouponRepositoryInterface $couponRepository,
        CouponExtensionFactory $magentoCouponExtensionFactory
    ) {
        $this->entityManager = $entityManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->salesruleRepository = $salesruleRepository;
        $this->couponInterfaceFactory = $couponInterfaceFactory;
        $this->couponGenerationSpecInterfaceFactory = $couponGenerationSpecInterfaceFactory;
        $this->couponManagement = $couponManagement;
        $this->couponRepository = $couponRepository;
        $this->magentoCouponExtensionFactory = $magentoCouponExtensionFactory;
    }

    /**
     * Generate coupon through Magento API
     *
     * @param int $ruleId
     * @param int|null $customerId
     * @param string $customerEmail
     * @param int $adminUserId
     * @return \Magento\SalesRule\Api\Data\CouponInterface|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateCoupon($ruleId, $customerId, $customerEmail, $adminUserId)
    {
        /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
        $salesruleDataObject = $this->salesruleRepository->get($ruleId);

        /** \Magento\SalesRule\Api\Data\CouponGenerationSpecInterface $couponGenerationSpecDataObject */
        $couponGenerationSpecDataObject = $this->couponGenerationSpecInterfaceFactory->create();
        $couponGenerationSpecDataObject
            ->setRuleId($salesruleDataObject->getRuleId())
            ->setFormat($salesruleDataObject->getCodeFormat())
            ->setQuantity(1)
            ->setLength($salesruleDataObject->getCouponLength())
            ->setPrefix($salesruleDataObject->getCodePrefix())
            ->setSuffix($salesruleDataObject->getCodeSuffix())
            ->setDelimiterAtEvery($salesruleDataObject->getCodeDash())
        ;

        $result = $this->couponManagement->generate($couponGenerationSpecDataObject);
        $code = array_shift($result);

        $this->searchCriteriaBuilder->addFilter('code', $code);
        $couponsList = $this->couponRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems()
        ;
        foreach ($couponsList as $couponData) {
            if (is_array($couponData)) {
                /** @var \Magento\SalesRule\Api\Data\CouponInterface $coupon */
                $coupon = $this->couponRepository->getById($couponData['coupon_id']);
            } else {
                $coupon = $couponData;
            }

            /** @var \Aheadworks\Coupongenerator\Api\Data\CouponInterface $couponDataObject */
            $couponDataObject = $this->couponInterfaceFactory->create();
            $couponDataObject
                ->setCouponId($coupon->getCouponId())
                ->setIsDeactivated(false)
                ->setAdminUserId($adminUserId)
                ->setRecipientEmail($customerEmail)
                ->setCustomerId($customerId)
            ;
            $this->entityManager->save($couponDataObject);

            return $coupon;
        }

        return false;
    }
}
