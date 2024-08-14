<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Rule\ObjectDataProcessors;

use Aheadworks\OnSale\Model\Rule\ObjectDataProcessors\CustomerGroup;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\OnSale\Model\Rule as RuleModel;
use Aheadworks\OnSale\Model\Source\Customer\Group as CustomerGroupSource;
use Aheadworks\OnSale\Api\Data\RuleInterface;

/**
 * Class CustomerGroupTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Rule\ObjectDataProcessors
 */
class CustomerGroupTest extends TestCase
{
    /**
     * @var CustomerGroup
     */
    private $model;

    /**
     * @var CustomerGroupSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerGroupSourceMock;

    /**
     * @var array
     */
    private $allCustomerGroups = [1, 2, 3, 4];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->customerGroupSourceMock = $this->createPartialMock(
            CustomerGroupSource::class,
            ['getAllCustomerGroupIds']
        );
        $this->customerGroupSourceMock->expects($this->any())
            ->method('getAllCustomerGroupIds')
            ->willReturn($this->allCustomerGroups);

        $this->model = $objectManager->getObject(
            CustomerGroup::class,
            [
                'customerGroupSource' => $this->customerGroupSourceMock
            ]
        );
    }

    /**
     * Test for beforeSave method
     *
     * @dataProvider beforeSaveDataProvider
     * @param array|string $customerGroups
     * @param string $result
     */
    public function testBeforeSave($customerGroups, $result)
    {
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->any())
            ->method('getCustomerGroups')
            ->willReturn($customerGroups);

        $ruleMock->expects($this->any())
            ->method('setCustomerGroups')
            ->with($result);

        $this->assertSame($ruleMock, $this->model->beforeSave($ruleMock));
    }

    /**
     * Data provider for beforeSave method
     */
    public function beforeSaveDataProvider()
    {
        $customerGroups1 = [1, 2, 3];
        $customerGroups2 = [0];
        return [
            'case 1' => [$customerGroups1, '1,2,3'],
            'case 2' => [$customerGroups2, CustomerGroupSource::ALL_GROUPS],
            'case 3' => ['1,2,3', '1,2,3'],
        ];
    }

    /**
     * Test for AfterLoad method
     *
     * @dataProvider afterLoadDataProvider
     * @param array|string $customerGroups
     * @param string $result
     */
    public function testAfterLoad($customerGroups, $result)
    {
        $ruleMock = $this->getMockForAbstractClass(RuleInterface::class);
        $ruleMock->expects($this->any())
            ->method('getCustomerGroups')
            ->willReturn($customerGroups);

        $ruleMock->expects($this->any())
            ->method('setCustomerGroups')
            ->with($result);

        $this->assertSame($ruleMock, $this->model->beforeSave($ruleMock));
    }

    /**
     * Data provider for beforeSave method
     */
    public function afterLoadDataProvider()
    {
        $customerGroups1 = [1, 3, 4];

        return [
            'case 1' => [CustomerGroupSource::ALL_GROUPS, $this->allCustomerGroups],
            'case 2' => ['1,3,4', $customerGroups1],
        ];
    }
}
