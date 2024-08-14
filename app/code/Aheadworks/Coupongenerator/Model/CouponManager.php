<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model;

use Aheadworks\Coupongenerator\Api\CouponManagerInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterfaceFactory;
use Aheadworks\Coupongenerator\Model\Coupon\Generator;
use Aheadworks\Coupongenerator\Model\Coupon\Sender as CouponSender;
use Aheadworks\Coupongenerator\Model\SalesruleRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class CouponManager
 * @package Aheadworks\Coupongenerator\Model
 */
class CouponManager implements CouponManagerInterface
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $adminSession;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    private $userFactory;

    /**
     * @var \Aheadworks\Coupongenerator\Model\SalesruleRepository
     */
    private $salesruleRepository;

    /**
     * @var \Aheadworks\Coupongenerator\Model\Coupon\Generator
     */
    private $couponGenerator;

    /**
     * @var \Aheadworks\Coupongenerator\Model\Coupon\Sender
     */
    private $couponSender;

    /**
     * @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterfaceFactory
     */
    private $couponGenerationResultFactory;

    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param Generator $couponGenerator
     * @param CouponSender $couponSender
     * @param \Aheadworks\Coupongenerator\Model\SalesruleRepository $salesruleRepository
     * @param RuleRepositoryInterface $ruleRepository
     * @param CouponGenerationResultInterfaceFactory $couponGenerationResultFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\User\Model\UserFactory $userFactory,
        Generator $couponGenerator,
        CouponSender $couponSender,
        SalesruleRepository $salesruleRepository,
        RuleRepositoryInterface $ruleRepository,
        CouponGenerationResultInterfaceFactory $couponGenerationResultFactory,
        CustomerRepositoryInterface $customerRepository,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->adminSession = $adminSession;
        $this->userFactory = $userFactory;
        $this->couponGenerator = $couponGenerator;
        $this->couponSender = $couponSender;
        $this->salesruleRepository = $salesruleRepository;
        $this->ruleRepository = $ruleRepository;
        $this->couponGenerationResultFactory = $couponGenerationResultFactory;
        $this->customerRepository = $customerRepository;
        $this->storeRepository = $storeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function generateForEmail($ruleId, $email, $isSendEmail = true)
    {
        $magentoRule = $this->getMagentoRule($ruleId);
        $storeId = $this->getRuleStoreId($magentoRule);

        /** @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface $couponGenerationResult */
        $couponGenerationResult = $this->generate($ruleId, null, $email, '', $storeId, $isSendEmail);

        return $couponGenerationResult;
    }

    /**
     * {@inheritdoc}
     */
    public function generateForCustomer($ruleId, $customerId, $isSendEmail = true)
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $this->customerRepository->getById($customerId);

        $magentoRule = $this->getMagentoRule($ruleId);
        $this->validateCustomer($customer, $magentoRule);

        /** @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface $couponGenerationResult */
        $couponGenerationResult = $this->generate(
            $ruleId,
            $customer->getId(),
            $customer->getEmail(),
            $customer->getFirstname(),
            $customer->getStoreId(),
            $isSendEmail
        );

        return $couponGenerationResult;
    }

    /**
     * @param int $ruleId
     * @param int|null $customerId
     * @param bool $customerEmail
     * @param string $customerName
     * @param int $storeId
     * @param bool $isSendEmail
     * @return \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function generate($ruleId, $customerId, $customerEmail, $customerName, $storeId, $isSendEmail = true)
    {
        /** @var \Magento\SalesRule\Api\Data\RuleInterface $magentoRule */
        $magentoRule = $this->getMagentoRule($ruleId);

        if (!$magentoRule->getIsActive()) {
            throw new LocalizedException(__('Rule %1 is not active', $magentoRule->getName()));
        }

        $adminUserId = $this->getAdminUserId();

        /** @var \Magento\SalesRule\Api\Data\CouponInterface $coupon */
        $coupon = $this->couponGenerator->generateCoupon($ruleId, $customerId, $customerEmail, $adminUserId);

        $resultMessages = [];

        /** @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface $couponGenerationResult */
        $couponGenerationResult = $this->couponGenerationResultFactory->create();

        if ($coupon) {
            $couponGenerationResult->setCoupon($coupon);

            $resultMessages[] =
                __('Coupon %1 has been generated for %2', $coupon->getCode(), $customerEmail);

            if ($isSendEmail) {
                $this->couponSender->sendCoupon($coupon, $customerName, $customerEmail, $storeId);
                $resultMessages[] =
                    __('Email with coupon %1 has been send to %2', $coupon->getCode(), $customerEmail);
            }
            $couponGenerationResult->setMessages($resultMessages);
        } else {
            $couponGenerationResult->setCoupon(null);
        }

        return $couponGenerationResult;
    }

    /**
     * Get magento rule by salesrule id
     *
     * @param int $salesruleId
     * @return \Magento\SalesRule\Api\Data\RuleInterface
     */
    private function getMagentoRule($salesruleId)
    {
        /** @var \Aheadworks\Coupongenerator\Api\Data\SalesruleInterface $salesruleDataObject */
        $salesruleDataObject = $this->salesruleRepository->get($salesruleId);

        /** @var \Magento\SalesRule\Api\Data\RuleInterface $magentoRule */
        $magentoRule = $this->ruleRepository->getById($salesruleDataObject->getRuleId());

        return $magentoRule;
    }

    /**
     * Validate customer with the rule specified
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param \Magento\SalesRule\Api\Data\RuleInterface $rule
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateCustomer($customer, $rule)
    {
        if ($rule->getRuleId()) {
            if (!in_array($customer->getWebsiteId(), $rule->getWebsiteIds())) {
                throw new LocalizedException(
                    __(
                        'The customer with e-mail %1 is not valid for the rule %2',
                        $customer->getEmail(),
                        $rule->getName()
                    )
                );
            }
        }
    }

    /**
     * Get current admin user id
     *
     * @return int
     */
    private function getAdminUserId()
    {
        if ($this->adminSession->getUser()) {
            $userId = $this->adminSession->getUser()->getUserId();
        } else {
            $userId = $this->userFactory->create()
                ->getCollection()
                ->addFieldToFilter('is_active', ['eq' => 1])
                ->getFirstItem()
                ->getUserId()
            ;
        }

        return $userId;
    }

    /**
     * Get store id from the rule specified
     *
     * @param \Magento\SalesRule\Api\Data\RuleInterface $rule
     * @return int
     */
    private function getRuleStoreId($rule)
    {
        $storeId = null;

        $ruleWebsites = $rule->getWebsiteIds();
        $websiteId = $ruleWebsites[0];

        $stores = $this->storeRepository->getList();
        foreach ($stores as $store) {
            if ($store->getWebsiteId() == $websiteId && $store->isActive()) {
                $storeId = $store->getId();
                break;
            }
        }

        return $storeId;
    }
}
