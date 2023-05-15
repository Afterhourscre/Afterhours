<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\ViewModel\Label;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\OnSale\Api\BlockRepositoryInterface;
use Aheadworks\OnSale\Api\Data\BlockInterface;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Block\Label\Renderer\Label;
use Aheadworks\OnSale\Model\Label\Renderer\ConfigMetadata;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Http\Context as HttpContext;
use Aheadworks\OnSale\Model\Source\Label\Position\Area as AreaSource;
use Aheadworks\OnSale\Model\Source\Label\Position as PositionSource;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\ViewModel\Label\Renderer;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Placement;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class RendererTest
 *
 * @package Aheadworks\OnSale\Test\Unit\ViewModel\Label
 */
class RendererTest extends TestCase
{
    /**
     * @var Renderer
     */
    private $model;

    /**
     * @var ConfigMetadata\Resolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMetadataResolverMock;

    /**
     * @var AreaSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $areaSourceMock;

    /**
     * @var PositionSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $positionSourceMock;

    /**
     * @var BlockRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $blockRepositoryMock;

    /**
     * @var LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->configMetadataResolverMock = $this->createPartialMock(
            ConfigMetadata\Resolver::class,
            ['resolveByPlacement', 'resolveByImage']
        );
        $this->areaSourceMock = $this->createPartialMock(AreaSource::class, ['getAreaValues', 'getPositionByArea']);
        $this->positionSourceMock = $this->createPartialMock(
            PositionSource::class,
            ['getClassByPosition', 'isInvertLabelByPosition']
        );
        $this->blockRepositoryMock = $this->getMockForAbstractClass(BlockRepositoryInterface::class);
        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);
        $this->model = $objectManager->getObject(
            Renderer::class,
            [
                'configMetadataResolver' => $this->configMetadataResolverMock,
                'areaSource' => $this->areaSourceMock,
                'positionSource' => $this->positionSourceMock,
                'blockRepository' => $this->blockRepositoryMock,
                'layout' => $this->layoutMock
            ]
        );
    }

    /**
     * Test getLabelAreas method
     */
    public function testGetLabelAreas()
    {
        $expected = [AreaSource::PRODUCT_IMAGE, AreaSource::NEXT_TO_PRICE];

        $this->areaSourceMock->expects($this->once())
            ->method('getAreaValues')
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getLabelAreas());
    }

    /**
     * Test getLabelConfig method
     *
     * @param string $area
     * @param string $placement
     * @param string $image
     * @throws LocalizedException
     * @dataProvider getLabelConfigDataProvider
     */
    public function testGetLabelConfig($area, $placement, $image)
    {
        $configMetadataMock = $this->getMockForAbstractClass(ConfigMetadata::class);
        $this->configMetadataResolverMock->expects($this->any())
            ->method('resolveByPlacement')
            ->with($placement, $area)
            ->willReturn($configMetadataMock);
        $this->configMetadataResolverMock->expects($this->any())
            ->method('resolveByImage')
            ->with($image, $area)
            ->willReturn($configMetadataMock);

        $this->assertEquals($configMetadataMock, $this->model->getLabelConfig($area, $placement, $image));
    }

    /**
     * Test getLabelConfig method on exception
     *
     * @expectedException \Exception
     */
    public function testGetLabelConfigOnException()
    {
        $area = AreaSource::PRODUCT_IMAGE;
        $placement = Placement::PRODUCT_PAGE;
        $image = 'test_image_id2';
        $exception = new \Exception('exception');

        $this->configMetadataResolverMock->expects($this->once())
            ->method('resolveByPlacement')
            ->with($placement, $area)
            ->willThrowException($exception);

        $this->model->getLabelConfig($area, $placement, $image);
    }

    /**
     * Test getLabelConfig method on exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testGetLabelConfigOnLocalizedException()
    {
        $area = AreaSource::PRODUCT_IMAGE;
        $this->model->getLabelConfig($area, null, null);
    }

    /**
     * Data provider for getLabelConfig test
     *
     * @return array
     */
    public function getLabelConfigDataProvider()
    {
        return [
            [AreaSource::PRODUCT_IMAGE, Placement::PRODUCT_PAGE, 'image_id1'],
            [AreaSource::NEXT_TO_PRICE, Placement::PRODUCT_LIST, 'image_id2'],
            [AreaSource::PRODUCT_IMAGE, null, 'image_id1'],
            [AreaSource::NEXT_TO_PRICE, Placement::PRODUCT_LIST, null]
        ];
    }

    /**
     * Test getCssByPosition method
     */
    public function testGetCssByPosition()
    {
        $position = PositionSource::NEXT_TO_PRICE;
        $expected = 'css';

        $this->positionSourceMock->expects($this->once())
            ->method('getClassByPosition')
            ->with($position)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->model->getCssByPosition($position));
    }

    /**
     * Test prepareLabelBlocksByPosition method
     *
     * @param BlockInterface[] $labelBlocks
     * @param string $position
     * @param bool $isInvertLabel
     * @param BlockInterface[] $expected
     * @dataProvider prepareLabelBlocksByPositionDataProvider
     */
    public function testPrepareLabelBlocksByPosition($labelBlocks, $position, $isInvertLabel, $expected)
    {
        $this->positionSourceMock->expects($this->once())
            ->method('isInvertLabelByPosition')
            ->with($position)
            ->willReturn($isInvertLabel);

        $this->assertEquals($expected, $this->model->prepareLabelBlocksByPosition($labelBlocks, $position));
    }

    /**
     * Data provider for getLabelConfig test
     *
     * @return array
     */
    public function prepareLabelBlocksByPositionDataProvider()
    {
        $blockMock1 = $this->getMockForAbstractClass(BlockInterface::class);
        $blockMock2 = $this->getMockForAbstractClass(BlockInterface::class);

        return [
            [
                [$blockMock1, $blockMock2],
                PositionSource::NEXT_TO_PRICE,
                false,
                [$blockMock1, $blockMock2]
            ],
            [
                [$blockMock1, $blockMock2],
                PositionSource::BOTTOM_RIGHT,
                true,
                [$blockMock2, $blockMock1]
            ],
            [
                [$blockMock1, $blockMock2],
                PositionSource::BOTTOM_LEFT,
                true,
                [$blockMock2, $blockMock1]
            ]
        ];
    }

    /**
     * Test getLabelBlocksForArea method
     */
    public function testGetLabelBlocksForArea()
    {
        $area = AreaSource::NEXT_TO_PRICE;
        $positionsByArea = [PositionSource::NEXT_TO_PRICE];
        $customerGroupId = 4;
        $productMock = $this->getMockForAbstractClass(ProductInterface::class);
        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $placement = Placement::PRODUCT_LIST;

        $blockMock1 = $this->getMockForAbstractClass(BlockInterface::class);
        $blockMock2 = $this->getMockForAbstractClass(BlockInterface::class);

        $labelMock1 = $this->getMockForAbstractClass(LabelInterface::class);
        $labelMock2 = $this->getMockForAbstractClass(LabelInterface::class);
        $labelPosition1 = PositionSource::BOTTOM_LEFT;
        $labelPosition2 = PositionSource::NEXT_TO_PRICE;

        $blocks = [$blockMock1, $blockMock2];
        $expected = [$labelPosition2 => [$blockMock2]];

        $this->areaSourceMock->expects($this->once())
            ->method('getPositionByArea')
            ->with($area)
            ->willReturn($positionsByArea);

        $this->initLabelBlocks($blocks);

        $blockMock1->expects($this->once())
            ->method('getLabel')
            ->willReturn($labelMock1);
        $labelMock1->expects($this->once())
            ->method('getPosition')
            ->willReturn($labelPosition1);

        $blockMock2->expects($this->once())
            ->method('getLabel')
            ->willReturn($labelMock2);
        $labelMock2->expects($this->once())
            ->method('getPosition')
            ->willReturn($labelPosition2);

        $this->assertEquals(
            $expected,
            $this->model->getLabelBlocksForArea($area, $placement, $productMock, $customerGroupId)
        );
    }

    /**
     * Test createLabelBlock method
     */
    public function testCreateLabelBlock()
    {
        $expected = '';
        $labelBlockMock = $this->getMockForAbstractClass(BlockInterface::class);
        $labelConfigMock = $this->createMock(ConfigMetadata::class);

        $labelMock = $this->createPartialMock(Label::class, ['setLabelBlock', 'setLabelConfig', 'toHtml']);
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(Label::class)
            ->willReturn($labelMock);

        $labelMock->expects($this->once())
            ->method('setLabelBlock')
            ->with($labelBlockMock)
            ->willReturnSelf();
        $labelMock->expects($this->once())
            ->method('setLabelConfig')
            ->with($labelConfigMock)
            ->willReturnSelf();

        $this->assertEquals($expected, $this->model->createLabelBlock($labelBlockMock, $labelConfigMock));
    }

    /**
     * Init label blocks
     *
     * @param BlockInterface[] $blocks
     * @return void
     */
    private function initLabelBlocks($blocks)
    {
        $this->blockRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($blocks);
    }
}
