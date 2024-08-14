<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Test\Unit\Model\EarnRule\ProductMatcher\ProductResolver;

use Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver\Simple;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\RewardPoints\Model\EarnRule\ProductMatcher\ProductResolver\Simple
 */
class SimpleTest extends TestCase
{
    /**
     * @var Simple
     */
    private $resolver;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resolver = $objectManager->getObject(Simple::class, []);
    }

    /**
     * Test getProductsForValidation method
     */
    public function testGetProductsForValidation()
    {
        $productMock = $this->createMock(Product::class);

        $this->assertEquals([$productMock], $this->resolver->getProductsForValidation($productMock));
    }
}
