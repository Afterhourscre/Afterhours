<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Aheadworks\Acr\Model\Template\VariableProcessor\CustomerData;
use Magento\Customer\Model\Customer as CustomerObject;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class CustomerDataTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class CustomerDataTest extends TestCase
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactoryMock;

    /**
     * @var ObjectManagerInterface
     */
    private $customerObjectMock;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepositoryInterfaceMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->customerCollectionFactoryMock = $this->createMock(CustomerCollectionFactory::class);
        $this->customerObjectMock = $this->createMock(CustomerObject::class);
        $this->customerRepositoryInterfaceMock = $this->createMock(CustomerRepositoryInterface::class);

        $this->customer = $objectManager->getObject(
            CustomerData::class,
            [
                'customerCollectionFactory' => $this->customerCollectionFactoryMock,
                'customerObject' => $this->customerObjectMock,
                'customerRepository' => $this->customerRepositoryInterfaceMock
            ]
        );
    }

    /**
     * Test Process method
     */
    public function testProcess()
    {
        $customerId = 1;
        $email  = 'email@test.com';
        $storeId  = 1;
        $customerGroupId  = 1;
        $customerFirstname  = 'test';
        $customerLastname  = 'test';
        $quote = $this->createMock(CartInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $quote->expects($this->exactly(2))
            ->method('getCustomer')
            ->willReturn($customer);
        $customer->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($customerId);
        $this->customerRepositoryInterfaceMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);

        $customer->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);
        $customer->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $customer->expects($this->once())
            ->method('getGroupId')
            ->willReturn($customerGroupId);
        $customer->expects($this->once())
            ->method('getFirstname')
            ->willReturn($customerFirstname);
        $customer->expects($this->once())
            ->method('getLastname')
            ->willReturn($customerLastname);

        $customerData = [
            'email'  => $email,
            'store_id'  => $storeId,
            'customer_group_id'  => $customerGroupId,
            'customer_firstname' => $customerFirstname,
            'customer_lastname' => $customerLastname,
        ];
        $this->assertSame($customerData, $this->customer->process($quote, []));
    }

    /**
     * Test testProcess method
     */
    public function testProcessTest()
    {
        $email  = 'email@test.com';
        $storeId  = 1;
        $customerGroupId  = 1;
        $customerFirstname  = 'test';
        $customerLastname  = 'test';
        $customerName  = 'test';
        $customer = $this->createMock(CustomerObject::class);
        $customerCollection = $this->createMock(CustomerCollection::class);

        $this->customerCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($customerCollection);
        $customerCollection->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($customer);

        $customer->expects($this->any())
            ->method('getData')
            ->willReturnMap(
                [
                    ['email', null, $email],
                    ['store_id', null, $storeId],
                    ['firstname', null, $customerFirstname],
                    ['lastname', null, $customerLastname],
                ]
            );
        $customer->expects($this->once())
            ->method('getGroupId')
            ->willReturn($customerGroupId);
        $customer->expects($this->once())
            ->method('getName')
            ->willReturn($customerName);

        $customerData = [
            'email'  => $email,
            'store_id'  => $storeId,
            'customer_group_id'  => $customerGroupId,
            'customer_firstname' => $customerFirstname,
            'customer_lastname' => $customerLastname,
            'customer_name' => $customerName,
        ];
        $this->assertSame($customerData, $this->customer->processTest([]));
    }
}
