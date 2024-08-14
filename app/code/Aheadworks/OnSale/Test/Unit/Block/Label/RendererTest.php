<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Block\Label;

use Aheadworks\OnSale\Api\Data\BlockInterface;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Block\Label\Renderer;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Placement;
use Magento\Catalog\Api\Data\ProductInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Api\BlockRepositoryInterface;
use Aheadworks\OnSale\Model\Label\Renderer\ConfigMetadata;
use Aheadworks\OnSale\Model\Label\Renderer\Product\Resolver as ProductResolver;
use Magento\Framework\App\Http\Context as HttpContext;
use Aheadworks\OnSale\Model\Source\Label\Position\Area as AreaSource;
use Aheadworks\OnSale\Model\Source\Label\Position as PositionSource;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\OnSale\ViewModel\Label\Renderer as RendererViewModel;

/**
 * Class Renderer
 *
 * @package Aheadworks\OnSale\Test\Unit\Block\Label
 */
class RendererTest extends TestCase
{
    /**
     * @var Renderer
     */
    private $model;

    /**
     * @var ProductResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productResolverMock;

    /**
     * @var HttpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpContextMock;

    /**
     * @var LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutMock;

    /**
     * @var RendererViewModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $viewModelMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->productResolverMock = $this->createPartialMock(ProductResolver::class, ['resolveByPlacement']);
        $this->httpContextMock = $this->createPartialMock(HttpContext::class, ['getValue']);
        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);
        $this->viewModelMock = $this->createMock(RendererViewModel::class);
        $contextMock = $objectManager->getObject(
            Context::class,
            [
                'layout' => $this->layoutMock
            ]
        );

        $this->model = $objectManager->getObject(
            Renderer::class,
            [
                'context' => $contextMock,
                'productResolver' => $this->productResolverMock,
                'httpContext' => $this->httpContextMock,
                'viewModel' => $this->viewModelMock
            ]
        );
    }

    /**
     * Test getLabelBlocksForArea method
     */
    public function testGetLabelBlocksForArea()
    {
        $area = AreaSource::NEXT_TO_PRICE;
        $placement = Placement::PRODUCT_LIST;
        $customerGroupId = 4;
        $productMock = $this->getMockForAbstractClass(ProductInterface::class);
        $blockMock1 = $this->getMockForAbstractClass(BlockInterface::class);
        $blockMock2 = $this->getMockForAbstractClass(BlockInterface::class);
        $this->model->setPlacement($placement);

        $this->productResolverMock->expects($this->once())
            ->method('resolveByPlacement')
            ->with($placement)
            ->willReturn($productMock);
        $this->productResolverMock->expects($this->once())
            ->method('resolveByPlacement')
            ->with($placement)
            ->willReturn($productMock);
        $this->httpContextMock->expects($this->once())
            ->method('getValue')
            ->with(CustomerContext::CONTEXT_GROUP)
            ->willReturn($customerGroupId);

        $this->viewModelMock->expects($this->once())
            ->method('getLabelBlocksForArea')
            ->with($area, $placement, $productMock, $customerGroupId)
            ->willReturn([$blockMock1, $blockMock2]);

        $this->assertEquals([$blockMock1, $blockMock2], $this->model->getLabelBlocksForArea($area));
    }

    /**
     * Test testGetProduct method
     *
     * @param bool $isProductSet
     * @dataProvider testGetValueDataProvider
     */
    public function testGetProduct($isProductSet)
    {
        $productMock = $this->getMockForAbstractClass(ProductInterface::class);
        if ($isProductSet) {
            $this->model->setProduct($productMock);
        } else {
            $placement = Placement::PRODUCT_LIST;
            $this->model->setPlacement($placement);
            $this->productResolverMock->expects($this->once())
                ->method('resolveByPlacement')
                ->with($placement)
                ->willReturn($productMock);
        }
        $this->assertEquals($productMock, $this->model->getProduct());
    }

    /**
     * Test testGetCustomerGroupId method
     *
     * @param bool $isCustomerGroupSet
     * @dataProvider testGetValueDataProvider
     */
    public function testGetCustomerGroupId($isCustomerGroupSet)
    {
        $customerGroupId = 1;
        if ($isCustomerGroupSet) {
            $this->model->setCustomerGroupId($customerGroupId);
        } else {
            $placement = Placement::PRODUCT_LIST;
            $this->model->setPlacement($placement);
            $this->httpContextMock->expects($this->once())
                ->method('getValue')
                ->with(CustomerContext::CONTEXT_GROUP)
                ->willReturn($customerGroupId);
        }
        $this->assertEquals($customerGroupId, $this->model->getCustomerGroupId());
    }

    /**
     * Data provider for getLabelConfig test
     *
     * @return array
     */
    public function testGetValueDataProvider()
    {
        return [
            [true],
            [false]
        ];
    }
}
