<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Test\Unit\Model\Calculator\Earning\EarnItemResolver;

use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemInterface;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessorPool;
use Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor\TypeProcessorInterface;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\RewardPoints\Model\Calculator\Earning\EarnItemResolver\ProductProcessor
 */
class ProductProcessorTest extends TestCase
{
    /**
     * @var ProductProcessor
     */
    private $processor;

    /**
     * @var TypeProcessorPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $typeProcessorPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->typeProcessorPoolMock = $this->createMock(TypeProcessorPool::class);

        $this->processor = $objectManager->getObject(
            ProductProcessor::class,
            [
                'typeProcessorPool' => $this->typeProcessorPoolMock,
            ]
        );
    }

    /**
     * Test getEarnItems method
     *
     * @param bool $beforeTax
     * @dataProvider getEarnItemsDataProvider
     * @throws \Exception
     */
    public function testGetEarnItems($beforeTax)
    {
        $productType = 'simple';

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn($productType);

        $earnItemMock = $this->createMock(EarnItemInterface::class);
        $earnItems = [$earnItemMock];

        $typeProcessorMock = $this->createMock(TypeProcessorInterface::class);
        $this->typeProcessorPoolMock->expects($this->once())
            ->method('getProcessorByCode')
            ->with($productType)
            ->willReturn($typeProcessorMock);

        $typeProcessorMock->expects($this->once())
            ->method('getEarnItems')
            ->with($productMock, $beforeTax)
            ->willReturn($earnItems);

        $this->assertEquals($earnItems, $this->processor->getEarnItems($productMock, $beforeTax));
    }

    /**
     * @return array
     */
    public function getEarnItemsDataProvider()
    {
        return [
            ['beforeTax' => true],
            ['beforeTax' => false],
        ];
    }

    /**
     * Test getEarnItems method if an exception occurs
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Type processor must implements TypeProcessorInterface
     */
    public function testGetEarnItemsException()
    {
        $productType = 'simple';

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn($productType);

        $this->typeProcessorPoolMock->expects($this->once())
            ->method('getProcessorByCode')
            ->with($productType)
            ->willThrowException(new \Exception('Type processor must implements TypeProcessorInterface'));

        $this->processor->getEarnItems($productMock, true);
    }
}
