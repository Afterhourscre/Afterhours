<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Rule\LabelText;

use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\OnSale\Model\Rule\LabelText\StoreValue\ObjectResolver as StoreValueObjectResolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\OnSale\Model\Rule\LabelText\StoreResolver;

/**
 * Class StoreResolverTest
 *
 * @package Aheadworks\OnSale\Model\Rule\LabelText
 */
class StoreResolverTest extends TestCase
{
    /**
     * @var StoreResolver
     */
    private $model;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var StoreValueObjectResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->dataObjectProcessorMock = $this->createPartialMock(DataObjectProcessor::class, ['buildOutputDataArray']);
        $this->objectResolverMock = $this->createPartialMock(
            StoreValueObjectResolver::class,
            ['resolve']
        );

        $this->model = $objectManager->getObject(
            StoreResolver::class,
            [
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'objectResolver' =>  $this->objectResolverMock
            ]
        );
    }

    /**
     * Test for getValuesByStoreId method
     *
     * @dataProvider getValueByStoreIdDataProvider
     * @param int $storeId
     * @param string $expectedValue
     */
    public function testGetValueByStoreId($storeId, $expectedValue)
    {
        $storeValuesArray1 = ['store_id' => Store::DEFAULT_STORE_ID, 'value_large' => 'default_value'];
        $storeValuesArray2 = ['store_id' => 2, 'value_large' => 'value2'];
        $storeValuesArray3 = ['store_id' => 3, 'value_large' => 'value3'];
        $storeValueArray = [$storeValuesArray1, $storeValuesArray2, $storeValuesArray3];
        $storeValuesObject1 = $this->createStoreValueObject($storeValuesArray1);
        $storeValuesObject2 = $this->createStoreValueObject($storeValuesArray2);
        $storeValuesObject3 = $this->createStoreValueObject($storeValuesArray3);

        $this->objectResolverMock->expects($this->any())
            ->method('resolve')
            ->withConsecutive([$storeValuesArray1], [$storeValuesArray2], [$storeValuesArray3])
            ->willReturnOnConsecutiveCalls($storeValuesObject1, $storeValuesObject2, $storeValuesObject3);

        $this->assertEquals($expectedValue, $this->model->getValueByStoreId($storeValueArray, $storeId));
    }

    /**
     * Data provider for testGetValueByStoreId method
     */
    public function getValueByStoreIdDataProvider()
    {
        $obj1 = $this->createStoreValueObject(['store_id' => 2, 'value_large' => 'value2']);
        $obj2 = $this->createStoreValueObject(['store_id' => 0, 'value_large' => 'default_value']);
        $obj3 = $this->createStoreValueObject(['store_id' => 10, 'value_large' => 'default_value']);

        return [
            'case 1' => ['store_id' => 2, 'obj' => $obj1],
            'case 2' => ['store_id' => Store::DEFAULT_STORE_ID, 'obj' => $obj2],
            'case 3' => ['store_id' => 10, 'obj' => $obj3],
        ];
    }

    /**
     * Create store values objects from array
     *
     * @param $storeValueArray
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createStoreValueObject($storeValueArray)
    {
        $storeValueObject = $this->getMockForAbstractClass(LabelTextStoreValueInterface::class);
        $storeValueObject->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeValueArray['store_id']);
        $storeValueObject->expects($this->any())
            ->method('getValueLarge')
            ->willReturn($storeValueArray['value_large']);
        return $storeValueObject;
    }
}
