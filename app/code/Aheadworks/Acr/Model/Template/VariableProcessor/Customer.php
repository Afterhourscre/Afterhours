<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Template\VariableProcessor;

use Magento\Framework\ObjectManagerInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer as CustomerObject;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class Customer
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class Customer implements VariableProcessorInterface
{
    const DEFAULT_CUSTOMER_NAME = 'Guest';

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var CustomerObject
     */
    private $customerObject;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory,
        CustomerObject $customerObject,
        CustomerFactory $customerFactory
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerObject = $customerObject;
        $this->customerFactory = $customerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        $customerId = $quote->getCustomer()->getId();
        if ($customerId) {
            $customer = $this->customerObject->load($customerId);
        } else {
            $customer = $this->customerFactory->create();
            $customer->setData('name', $params['customer_name']);
            $customer->setData('firstname', $params['customer_name']);
            $customer->setData('lastname', $params['customer_name']);
        }
        return [Variables::CUSTOMER => $customer];
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return [Variables::CUSTOMER => $this->customerCollectionFactory->create()->getFirstItem()];
    }
}
