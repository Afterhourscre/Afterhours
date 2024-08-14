<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Test\Unit\Plugin\Block;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\OnSale\Plugin\Block\ListProductPlugin;
use Aheadworks\OnSale\Block\Label\RendererFactory as LabelRendererFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Aheadworks\OnSale\Block\Label\Renderer as LabelRenderer;

/**
 * Class ListProductPluginTest
 *
 * @package Aheadworks\OnSale\Test\Unit\Plugin\Block
 */
class ListProductPluginTest extends TestCase
{
    /**
     * Constant defined for testing
     */
    const TEST_HTML = '<p>test</p>';

    /**
     * @var ListProductPlugin|\PHPUnit_Framework_MockObject_MockObject
     */
    private $plugin;

    /**
     * @var LabelRendererFactory
     */
    private $labelRendererFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->labelRendererFactoryMock = $this->createPartialMock(LabelRendererFactory::class, ['create']);
        $this->plugin = $objectManager->getObject(
            ListProductPlugin::class,
            [
                'labelRendererFactory' => $this->labelRendererFactoryMock,
            ]
        );
    }

    /**
     * Test for aroundGetProductDetailsHtml method
     */
    public function testAroundGetProductDetailsHtml()
    {
        $productMock = $this->getMockForAbstractClass(ProductInterface::class);
        $productHtml = '<p>product</p>';

        $closureCalled = false;
        $proceed = function ($query) use (&$closureCalled, $productMock) {
            $closureCalled = true;
            $this->assertEquals($productMock, $query);
            return self::TEST_HTML;
        };

        $labelRendererMock = $this->createPartialMock(LabelRenderer::class, ['toHtml']);
        $this->labelRendererFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($labelRendererMock);
        $labelRendererMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($productHtml);

        $this->assertSame(
            self::TEST_HTML . $productHtml,
            $this->plugin->aroundGetProductDetailsHtml($productMock, $proceed, $productMock)
        );
    }
}
