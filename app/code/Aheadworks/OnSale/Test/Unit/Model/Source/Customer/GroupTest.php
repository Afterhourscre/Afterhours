<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Source\Customer;

use Aheadworks\OnSale\Model\Source\Customer\Group;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Customer\Model\ResourceModel\Group\Collection;

/**
 * Class GroupTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Source\Customer
 */
class GroupTest extends TestCase
{
    /**
     * @var Group
     */
    private $model;

    /**
     * @var Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerGroupCollectionMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->customerGroupCollectionMock = $this->createPartialMock(
            Collection::class,
            ['toOptionArray', 'getAllIds']
        );
        $this->model = $objectManager->getObject(
            Group::class,
            [
                'customerGroupCollection' => $this->customerGroupCollectionMock
            ]
        );
    }

    /**
     * Test getAllCustomerGroupIds method
     */
    public function testGetAllCustomerGroupIds()
    {
        $expected = [];
        $this->customerGroupCollectionMock->expects($this->once())
            ->method('getAllIds')
            ->willReturn($expected);

        $this->assertTrue(is_array($this->model->getAllCustomerGroupIds()));
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $customerGroups = [];
        $this->customerGroupCollectionMock->expects($this->once())
            ->method('toOptionArray')
            ->willReturn($customerGroups);

        $this->assertTrue(is_array($this->model->toOptionArray()));
    }
}
