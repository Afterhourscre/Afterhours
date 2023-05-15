<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Template\VariableProcessor;

use Magento\Framework\ObjectManagerInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class CustomerData
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class CustomerData implements VariableProcessorInterface
{
    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param ObjectManagerInterface $objectManager
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory,
        ObjectManagerInterface $objectManager,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->objectManager = $objectManager;
        $this->customerRepository = $customerRepositoryInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        if ($quote->getCustomer()->getId()) {
            $customer = $this->customerRepository->getById($quote->getCustomer()->getId());
            $customerData = [
                'email'  => $customer->getEmail(),
                'store_id'  => $customer->getStoreId(),
                'customer_group_id'  => $customer->getGroupId(),
                'customer_firstname' => $customer->getFirstname(),
                'customer_lastname' => $customer->getLastname(),
            ];
        } else {
            $customerData['customer_firstname'] = $params['customer_name'];
            $customerData['customer_lastname'] = $params['customer_name'];
            $customerData['customer_name'] = $params['customer_name'];
        }
        return $customerData;
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        $customerData = [];
        $customer = $this->customerCollectionFactory->create()->getFirstItem();
        $customerData = [
            'email'  => $customer->getData('email'),
            'store_id'  => $customer->getData('store_id'),
            'customer_group_id'  => $customer->getGroupId(),
            'customer_firstname' => $customer->getData('firstname'),
            'customer_lastname' => $customer->getData('lastname'),
            'customer_name' => $customer->getName(),
        ];
        return $customerData;
    }
}
