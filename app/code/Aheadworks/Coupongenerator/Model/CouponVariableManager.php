<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model;

use Aheadworks\Coupongenerator\Api\CouponManagerInterface;
use Aheadworks\Coupongenerator\Api\CouponVariableProcessorInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreRepositoryInterface;
use Aheadworks\Coupongenerator\Api\Data\CouponVariableInterfaceFactory;

/**
 * Class CouponVariableManager
 * @package Aheadworks\Coupongenerator\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponVariableManager extends AbstractCouponVariableManager
{
    /**
     * @var CouponManagerInterface
     */
    private $couponManager;

    /**
     * @var \Aheadworks\Coupongenerator\Api\CouponVariableProcessorInterface
     */
    private $couponVariableProcessor;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var CouponVariableInterfaceFactory
     */
    private $couponVariableFactory;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface|string
     */
    private $recipient;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    private $store;

    /**
     * @param CouponManagerInterface $couponManager
     * @param CouponVariableProcessorInterface $couponVariableProcessor
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreRepositoryInterface $storeRepository
     * @param CouponVariableInterfaceFactory $couponVariableFactory
     * @param array $data
     */
    public function __construct(
        CouponManagerInterface $couponManager,
        CouponVariableProcessorInterface $couponVariableProcessor,
        CustomerRepositoryInterface $customerRepository,
        StoreRepositoryInterface $storeRepository,
        CouponVariableInterfaceFactory $couponVariableFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->couponManager = $couponManager;
        $this->couponVariableProcessor = $couponVariableProcessor;
        $this->customerRepository = $customerRepository;
        $this->storeRepository = $storeRepository;
        $this->couponVariableFactory = $couponVariableFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function generateCoupon($ruleId = null, $alias = null)
    {
        $couponAlias = $this->parseCouponAlias($alias);

        /** @var \Aheadworks\Coupongenerator\Api\Data\CouponVariableInterface $couponVariable */
        $couponVariable = $this->couponVariableFactory->create();

        if ($ruleId && $this->recipient !== null) {
            try {
                if (is_object($this->recipient)) {
                    /** @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface $result */
                    $result = $this->couponManager->generateForCustomer($ruleId, $this->recipient->getId(), false);
                } else {
                    /** @var \Aheadworks\Coupongenerator\Api\Data\CouponGenerationResultInterface $result */
                    $result = $this->couponManager->generateForEmail($ruleId, $this->recipient, false);
                }

                if ($result->getCoupon()) {
                    $couponVariable = $this->couponVariableProcessor
                        ->getCouponVariable($result->getCoupon(), $this->store->getId())
                    ;
                }
            } catch (\Exception $e) {
            }
        }

        if ($couponAlias) {
            $this->couponsData[$couponAlias] = $couponVariable;
        } else {
            $this->couponsData[] = $couponVariable;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientByEmail($recipientEmail)
    {
        $this->recipient = $recipientEmail;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientByCustomerId($customerId)
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $this->customerRepository->getById($customerId);
        $this->recipient = $customer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        try {
            $store = $this->storeRepository->getById($storeId);

            if ($store->getId()) {
                $this->store = $store;
            }
        } catch (NoSuchEntityException $e) {
        }

        return $this;
    }
}
