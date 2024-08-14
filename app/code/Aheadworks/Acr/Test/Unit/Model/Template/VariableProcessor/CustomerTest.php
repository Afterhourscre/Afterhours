<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Customer\Model\CustomerFactory;
use Aheadworks\Acr\Model\Template\VariableProcessor\Customer;
use Magento\Customer\Model\Customer as CustomerObject;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class CustomerTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class CustomerTest extends TestCase
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
     * @var CustomerFactory
     */
    private $customerFactoryMock;

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
        $this->customerFactoryMock = $this->createMock(CustomerFactory::class);

        $this->customer = $objectManager->getObject(
            Customer::class,
            [
                'customerCollectionFactory' => $this->customerCollectionFactoryMock,
                'customerObject' => $this->customerObjectMock,
                'customerFactory' => $this->customerFactoryMock
            ]
        );
    }

    /**
     * Test Process method
     * @dataProvider data
     */
    public function testProcess($data)
    {
        $customerName = 'test';
        $customerId = $data ? 1 : null;
        $quote = $this->createMock(CartInterface::class);
        $customer = $this->createMock(CustomerObject::class);
        $customer2 = $this->createMock(CustomerObject::class);
        $quote->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customer);
        $customer->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);
        if ($data) {
            $this->customerObjectMock->expects($this->once())
                ->method('load')
                ->with($customerId)
                ->willReturn($customer2);
        } else {
            $this->customerFactoryMock->expects($this->once())
                ->method('create')
                ->willReturn($customer2);
            $customer2->expects($this->any())
                ->method('setData')
                ->withAnyParameters()
                ->willReturnSelf();
        }
        $this->assertSame(
            [Variables::CUSTOMER => $customer2],
            $this->customer->process($quote, ['customer_name' => $customerName])
        );
    }

    /**
     * Test ProcessTest method
     */
    public function testProcessTest()
    {
        $customerCollection = $this->createMock(CustomerCollection::class);
        $customer = $this->createMock(CustomerObject::class);
        $this->customerCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($customerCollection);
        $customerCollection->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($customer);
        $this->assertSame([Variables::CUSTOMER => $customer], $this->customer->processTest([]));
    }

    /**
     * @return array
     */
    public function data()
    {
        return [
            [true], [false]
        ];
    }
}
