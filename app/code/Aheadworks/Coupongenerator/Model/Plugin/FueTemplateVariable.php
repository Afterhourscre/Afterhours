<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Plugin;

use Aheadworks\Coupongenerator\Api\CouponVariableManagerInterfaceFactory;
use Aheadworks\Coupongenerator\Model\TestCouponVariableManagerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class FueTemplateVariable
 * @package Aheadworks\Coupongenerator\Model\Plugin
 * @codeCoverageIgnore
 */
class FueTemplateVariable
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var CouponVariableManagerInterfaceFactory
     */
    private $couponVariableManagerFactory;

    /**
     * @var TestCouponVariableManagerFactory
     */
    private $testCouponVariableManagerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $recipientEmail;

    /**
     * @var int
     */
    private $recipientStoreId;

    /**
     * @param RequestInterface $request
     * @param CouponVariableManagerInterfaceFactory $couponVariableManagerFactory
     * @param TestCouponVariableManagerFactory $testCouponVariableManagerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        RequestInterface $request,
        CouponVariableManagerInterfaceFactory $couponVariableManagerFactory,
        TestCouponVariableManagerFactory $testCouponVariableManagerFactory,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer
    ) {
        $this->request = $request;
        $this->couponVariableManagerFactory = $couponVariableManagerFactory;
        $this->testCouponVariableManagerFactory = $testCouponVariableManagerFactory;
        $this->customerRepository = $customerRepository;
        $this->serializer = $serializer;
    }

    /**
     * Set test coupon variable manager to email data
     *
     * @param \Aheadworks\Followupemail2\Model\Template\Variable $type
     * @param array $emailData
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetTestVariableData($type, $emailData)
    {
        /** @var \Aheadworks\Coupongenerator\Api\CouponVariableManagerInterface $couponVariableManager */
        $testCouponVariableManager = $this->testCouponVariableManagerFactory->create();
        $emailData['coupongenerator'] = $testCouponVariableManager;

        return $emailData;
    }

    /**
     * Get required data from FUE queue item
     *
     * @param \Aheadworks\Followupemail2\Model\Template\Variable $type
     * @param \Aheadworks\Followupemail2\Api\Data\EventQueueInterface $queueItem
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetVariableData($type, $queueItem)
    {
        $eventData = $this->serializer->unserialize($queueItem->getEventData());
        $this->recipientEmail =  $eventData['email'];
        $this->recipientStoreId = $eventData['store_id'];
    }

    /**
     * Set coupon variable manager to email data
     *
     * @param \Aheadworks\Followupemail2\Model\Template\Variable $type
     * @param array $emailData
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetVariableData($type, $emailData)
    {
        /** @var \Aheadworks\Coupongenerator\Model\TestCouponVariableManager $couponVariableManager */
        $couponVariableManager = $this->couponVariableManagerFactory->create();

        if (isset($emailData['customer'])) {
            $customer = $emailData['customer'];
            try {
                $validCustomer = $this->customerRepository->getById($customer->getId());
                $couponVariableManager
                    ->setRecipientByCustomerId($validCustomer->getId())
                    ->setStoreId($validCustomer->getStoreId());
            } catch (NoSuchEntityException $e) {
            }
        } else {
            $couponVariableManager
                ->setRecipientByEmail($this->recipientEmail)
                ->setStoreId($this->recipientStoreId);
        }
        $emailData['coupongenerator'] = $couponVariableManager;

        return $emailData;
    }
}
