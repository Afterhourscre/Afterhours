<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Template\VariableProcessor\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class StoreTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class StoreTest extends TestCase
{
    /**
     * @var Store
     */
    private $store;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->store = $objectManager->getObject(
            Store::class,
            [
                'storeManager' => $this->storeManagerMock,
            ]
        );
    }

    /**
     * Test Process method
     */
    public function testProcess()
    {
        $storeId = 1;
        $quote = $this->createMock(CartInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $store = $this->createMock(StoreManagerInterface::class);

        $quote->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customer);
        $customer->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($store);

        $this->assertSame([Variables::STORE => $store], $this->store->process($quote, []));
    }

    /**
     * Test testProcess method
     */
    public function testProcessTest()
    {
        $storeId = 1;
        $store = $this->createMock(StoreManagerInterface::class);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($store);

        $this->assertSame([Variables::STORE => $store], $this->store->processTest(['store_id' => $storeId]));
    }
}
