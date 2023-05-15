<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Test\Unit\Model\EarnRule\ProductMatcher;

use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver;
use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver\Pool;
use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolverInterface;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver
 */
class ProductResolverTest extends TestCase
{
    /**
     * @var ProductResolver
     */
    private $productResolver;

    /**
     * @var Pool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $poolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->poolMock = $this->createMock(Pool::class);

        $this->productResolver = $objectManager->getObject(
            ProductResolver::class,
            [
                'pool' => $this->poolMock,
            ]
        );
    }

    /**
     * Test getProductsForValidation method
     */
    public function testGetProductsForValidation()
    {
        $productType = 'configurable';

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn($productType);
        $childProductMock = $this->createMock(Product::class);

        $productsForValidation = [$childProductMock];

        $resolverMock = $this->createMock(ProductResolverInterface::class);
        $this->poolMock->expects($this->once())
            ->method('getResolverByCode')
            ->with($productType)
            ->willReturn($resolverMock);

        $resolverMock->expects($this->once())
            ->method('getProductsForValidation')
            ->with($productMock)
            ->willReturn($productsForValidation);

        $this->assertEquals($productsForValidation, $this->productResolver->getProductsForValidation($productMock));
    }

    /**
     * Test getProductsForValidation method if an exception occurs
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Product resolver must implements ProductResolverInterface
     */
    public function testGetProductsForValidationException()
    {
        $productType = 'configurable';

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn($productType);

        $this->poolMock->expects($this->once())
            ->method('getResolverByCode')
            ->with($productType)
            ->willThrowException(new \Exception('Product resolver must implements ProductResolverInterface'));

        $this->productResolver->getProductsForValidation($productMock);
    }
}
