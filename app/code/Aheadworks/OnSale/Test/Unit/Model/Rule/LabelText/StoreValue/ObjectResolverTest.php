<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Rule\LabelText\StoreValue;

use Aheadworks\OnSale\Model\Rule\LabelText\StoreValue\ObjectResolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class ObjectResolverTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Rule\LabelText\StoreValue
 */
class ObjectResolverTest extends TestCase
{
    /**
     * @var ObjectResolver
     */
    private $model;

    /**
     * @var LabelTextStoreValueInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $labelTextStoreValueFactoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->labelTextStoreValueFactoryMock = $this->createPartialMock(
            LabelTextStoreValueInterfaceFactory::class,
            ['create']
        );
        $this->dataObjectHelperMock = $this->createPartialMock(
            DataObjectHelper::class,
            ['populateWithArray']
        );
        $this->model = $objectManager->getObject(
            ObjectResolver::class,
            [
                'storeValueFactory' => $this->labelTextStoreValueFactoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock
            ]
        );
    }

    /**
     * Test resolve method
     *
     * @param array|LabelTextStoreValueInterface $storeValueItem
     * @param LabelTextStoreValueInterface $expected
     * @dataProvider resolveDataProvider
     */
    public function testResolve($storeValueItem, $expected)
    {
        if (is_array($storeValueItem)) {
            $this->labelTextStoreValueFactoryMock->expects($this->once())
                ->method('create')
                ->willReturn($expected);
            $this->dataObjectHelperMock->expects($this->once())
                ->method('populateWithArray')
                ->with($expected, $storeValueItem, LabelTextStoreValueInterface::class);
        }

        $this->assertEquals($expected, $this->model->resolve($storeValueItem));
    }

    /**
     * Data provider for resolve
     *
     * @return array
     */
    public function resolveDataProvider()
    {
        $storeValueItem = $this->getMockForAbstractClass(LabelTextStoreValueInterface::class);
        return [
            [
                $storeValueItem,
                $storeValueItem
            ],
            [
                [
                    LabelTextStoreValueInterface::STORE_ID => 1,
                    LabelTextStoreValueInterface::VALUE_LARGE => 'value_large',
                    LabelTextStoreValueInterface::VALUE_MEDIUM => 'value_medium',
                    LabelTextStoreValueInterface::VALUE_LARGE => 'value_small',
                ],
                $storeValueItem
            ]
        ];
    }
}
