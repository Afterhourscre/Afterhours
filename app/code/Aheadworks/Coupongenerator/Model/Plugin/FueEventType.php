<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Model\Plugin;

use Aheadworks\Coupongenerator\Api\CouponVariableManagerInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class FueEventType
 * @package Aheadworks\Coupongenerator\Model\Plugin
 * @codeCoverageIgnore
 */
class FueEventType
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Aheadworks\Coupongenerator\Api\CouponVariableManagerInterfaceFactory
     */
    private $couponVariableManagerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var string
     */
    private $recipientEmail;

    /**
     * @var int
     */
    private $recipientStoreId;

    /**
     * @var string
     */
    private $recipientName;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param CouponVariableManagerInterfaceFactory $couponVariableManagerFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        CouponVariableManagerInterfaceFactory $couponVariableManagerFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->request = $request;
        $this->couponVariableManagerFactory = $couponVariableManagerFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get required data from FUE queue item
     *
     * @param \Aheadworks\Followupemail\Model\Event\Type\TypeAbstract $type
     * @param \Aheadworks\Followupemail\Model\Queue $queueItem
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetEmailData($type, $queueItem)
    {
        if ($this->request->getActionName() != 'preview') {
            $this->recipientEmail = $queueItem->getRecipientEmail();
            $this->recipientStoreId = $queueItem->getStoreId();
            $this->recipientName = $queueItem->getRecipientName();
        }
    }

    /**
     * Set coupon variable manager to email data
     *
     * @param \Aheadworks\Followupemail\Model\Event\Type\TypeAbstract $type
     * @param array $emailData
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetEmailData($type, $emailData)
    {
        if ($this->request->getActionName() != 'preview') {
            /** @var \Aheadworks\Coupongenerator\Api\CouponVariableManagerInterface $couponVariableManager */
            $couponVariableManager = $this->couponVariableManagerFactory->create();

            if (isset($emailData['customer'])) {
                $customer = $emailData['customer'];
                try {
                    $validCustomer = $this->customerRepository->getById($customer->getId());
                    $couponVariableManager
                        ->setRecipientByCustomerId($validCustomer->getId())
                        ->setStoreId($this->recipientStoreId)
                    ;
                } catch (NoSuchEntityException $e) {
                }
            } else {
                $couponVariableManager
                    ->setRecipientByEmail($this->recipientEmail)
                    ->setStoreId($this->recipientStoreId)
                ;
            }
            $emailData['coupongenerator'] = $couponVariableManager;
        }

        return $emailData;
    }
}
