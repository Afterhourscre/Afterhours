<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price;

use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price\Configurable;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceInfo\Base as BasePrice;
use Magento\Framework\Pricing\Amount\Base as BaseAmount;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Catalog\Pricing\Price\SpecialPrice;
use Magento\Tax\Pricing\Adjustment;

/**
 * Class ConfigurableTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price.
 */
class ConfigurableTest extends TestCase
{
    /**
     * @var Configurable
     */
    private $model;

    /**
     * @var LowestPriceOptionsProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $lowestPriceOptionsProviderMock;

    /**
     * @var ProductInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->lowestPriceOptionsProviderMock = $this->getMockForAbstractClass(
            LowestPriceOptionsProviderInterface::class
        );

        $this->productMock = $this->createPartialMock(Product::class, ['getPriceInfo']);

        $this->model = $objectManager->getObject(
            Configurable::class,
            [
                'lowestPriceOptionsProvider' => $this->lowestPriceOptionsProviderMock
            ]
        );
    }

    /**
     * Test getRegularPrice method
     */
    public function testGetRegularPrice()
    {
        $regularPrice1 = 20;
        $regularPrice2 = 30;
        $result = 20;
        $lowestPriceProduct1 = $this->createPartialMock(Product::class, ['getPriceInfo']);
        $lowestPriceProduct2 = $this->createPartialMock(Product::class, ['getPriceInfo']);
        $products = [$lowestPriceProduct1, $lowestPriceProduct2];
        $this->lowestPriceOptionsProviderMock->expects($this->once())
            ->method('getProducts')
            ->with($this->productMock)
            ->willReturn($products);

        $this->prepareProduct($lowestPriceProduct1, RegularPrice::PRICE_CODE, $regularPrice1);
        $this->prepareProduct($lowestPriceProduct2, RegularPrice::PRICE_CODE, $regularPrice2);

        $this->assertSame($result, $this->model->getRegularPrice($this->productMock));
    }

    /**
     * Test getSpecialPrice method
     */
    public function testGetSpecialPrice()
    {
        $regularPrice1 = 60;
        $regularPrice2 = 45.5;
        $result = 45.5;
        $lowestPriceProduct1 = $this->createPartialMock(Product::class, ['getPriceInfo']);
        $lowestPriceProduct2 = $this->createPartialMock(Product::class, ['getPriceInfo']);
        $products = [$lowestPriceProduct1, $lowestPriceProduct2];
        $this->lowestPriceOptionsProviderMock->expects($this->once())
            ->method('getProducts')
            ->with($this->productMock)
            ->willReturn($products);

        $this->prepareProduct($lowestPriceProduct1, SpecialPrice::PRICE_CODE, $regularPrice1);
        $this->prepareProduct($lowestPriceProduct2, SpecialPrice::PRICE_CODE, $regularPrice2);

        $this->assertSame($result, $this->model->getSpecialPrice($this->productMock));
    }

    /**
     * Prepare product data for call parent method
     *
     * @param Product|\PHPUnit_Framework_MockObject_MockObject $product
     * @param string $priceType
     * @param float $price
     */
    public function prepareProduct($product, $priceType, $price)
    {
        $priceInfoMock = $this->createPartialMock(BasePrice::class, ['getPrice']);
        $regularPrice = $this->createPartialMock(RegularPrice::class, ['getAmount']);
        $amount = $this->createPartialMock(BaseAmount::class, ['getValue']);

        $product->expects($this->once())
            ->method('getPriceInfo')
            ->willReturn($priceInfoMock);
        $priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with($priceType)
            ->willReturn($regularPrice);
        $regularPrice->expects($this->once())
            ->method('getAmount')
            ->willReturn($amount);
        $amount->expects($this->once())
            ->method('getValue')
            ->with(Adjustment::ADJUSTMENT_CODE)
            ->willReturn($price);
    }
}
