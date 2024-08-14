<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule;

use Aheadworks\OnSale\Model\Label\Block\Rule\Counter;
use Aheadworks\OnSale\Model\Source\Label\Position;
use Magento\Store\Api\Data\StoreInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Model\Config;
use Aheadworks\OnSale\Model\Source\Label\Position\Area as PositionArea;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CounterTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Model\Label\Block\Rule
 */
class CounterTest extends TestCase
{
    /**
     * @var Counter
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var PositionArea|\PHPUnit_Framework_MockObject_MockObject
     */
    private $positionAreaMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
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
        $this->configMock = $this->createPartialMock(Config::class, ['getMaxNumberOfLabelsByArea']);
        $this->positionAreaMock = $this->createPartialMock(PositionArea::class, ['getAreaByPosition', 'getAreaValues']);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);

        $this->model = $objectManager->getObject(
            Counter::class,
            [
                'config' => $this->configMock,
                'positionArea' => $this->positionAreaMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    /**
     * Test reset method
     */
    public function testReset()
    {
        $expected = [PositionArea::PRODUCT_IMAGE, PositionArea::NEXT_TO_PRICE];

        $this->positionAreaMock->expects($this->once())
            ->method('getAreaValues')
            ->willReturn($expected);

        $this->model->reset();
    }

    /**
     * Test isLimitReached method
     *
     * @param string $position
     * @param string $area
     * @param int $maxNumberByArea
     * @param bool $expected
     * @dataProvider isLimitReachedDataProvider
     */
    public function testIsLimitReached($position, $area, $maxNumberByArea, $expected)
    {
        $areaValues = [PositionArea::PRODUCT_IMAGE, PositionArea::NEXT_TO_PRICE];
        $storeId = 1;
        $websiteId = 1;

        $this->positionAreaMock->expects($this->once())
            ->method('getAreaValues')
            ->willReturn($areaValues);
        $this->model->reset();

        $this->positionAreaMock->expects($this->once())
            ->method('getAreaByPosition')
            ->with($position)
            ->willReturn($area);
        $this->configMock->expects($this->once())
            ->method('getMaxNumberOfLabelsByArea')
            ->with($area, $websiteId)
            ->willReturn($maxNumberByArea);

        $storeMock = $this->getMockForAbstractClass(StoreInterface::class);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($storeMock);

        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->assertEquals($expected, $this->model->isLimitReached($position, $storeId));
    }

    /**
     * Data provider for isLimitReached test
     *
     * @return array
     */
    public function isLimitReachedDataProvider()
    {
        return [
            [Position::BOTTOM_RIGHT, PositionArea::PRODUCT_IMAGE, 1, false],
            [Position::NEXT_TO_PRICE, PositionArea::NEXT_TO_PRICE, 0, true]
        ];
    }
}
