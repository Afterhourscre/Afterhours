<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price;

use Magento\Tax\Pricing\Adjustment;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Catalog\Pricing\Price\SpecialPrice;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price\DefaultType;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceInfo\Base as BasePrice;
use Magento\Framework\Pricing\Amount\Base as BaseAmount;

/**
 * Class DefaultTypeTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule\Label\VariableProcessor\ProductType\Price
 */
class DefaultTypeTest extends TestCase
{
    /**
     * @var DefaultType
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->model = $objectManager->getObject(
            DefaultType::class,
            []
        );
    }

    /**
     * Test getRegularPrice method
     */
    public function testGetRegularPrice()
    {
        $result = '20';
        $productMock = $this->createPartialMock(Product::class, ['getPriceInfo']);
        $priceInfoMock = $this->createPartialMock(BasePrice::class, ['getPrice']);
        $regularPrice = $this->createPartialMock(RegularPrice::class, ['getAmount']);
        $amount = $this->createPartialMock(BaseAmount::class, ['getValue']);

        $productMock->expects($this->once())
            ->method('getPriceInfo')
            ->willReturn($priceInfoMock);
        $priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with(RegularPrice::PRICE_CODE)
            ->willReturn($regularPrice);
        $regularPrice->expects($this->once())
            ->method('getAmount')
            ->willReturn($amount);
        $amount->expects($this->once())
            ->method('getValue')
            ->with(Adjustment::ADJUSTMENT_CODE)
            ->willReturn($result);

        $this->model->getRegularPrice($productMock);
    }

    /**
     * Test getSpecialPrice method
     */
    public function testGetSpecialPrice()
    {
        $result = '400';
        $productMock = $this->createPartialMock(Product::class, ['getPriceInfo']);
        $priceInfoMock = $this->createPartialMock(BasePrice::class, ['getPrice']);
        $regularPrice = $this->createPartialMock(RegularPrice::class, ['getAmount']);
        $amount = $this->createPartialMock(BaseAmount::class, ['getValue']);

        $productMock->expects($this->once())
            ->method('getPriceInfo')
            ->willReturn($priceInfoMock);
        $priceInfoMock->expects($this->once())
            ->method('getPrice')
            ->with(SpecialPrice::PRICE_CODE)
            ->willReturn($regularPrice);
        $regularPrice->expects($this->once())
            ->method('getAmount')
            ->willReturn($amount);
        $amount->expects($this->once())
            ->method('getValue')
            ->with(Adjustment::ADJUSTMENT_CODE)
            ->willReturn($result);

        $this->model->getSpecialPrice($productMock);
    }
}
