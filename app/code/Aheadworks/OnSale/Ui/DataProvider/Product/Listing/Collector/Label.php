<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\DataProvider\Product\Listing\Collector;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorInterface;
use Aheadworks\OnSale\Block\Label\Renderer as LabelRenderer;
use Aheadworks\OnSale\Block\Label\RendererFactory as LabelRendererFactory;
use Magento\Quote\Api\Data\TotalsItemExtensionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductRender\ImageExtensionFactory;

/**
 * Class Label
 *
 * @package Aheadworks\OnSale\Ui\DataProvider\Product\Listing\Collector
 */
class Label implements ProductRenderCollectorInterface
{
    /**
     * @var LabelRendererFactory
     */
    private $labelRendererFactory;

    /**
     * @var ImageExtensionFactory
     */
    private $imageExtensionFactory;

    /**
     * @param LabelRendererFactory $labelRendererFactory
     * @param ImageExtensionFactory $imageExtensionFactory
     */
    public function __construct(
        LabelRendererFactory $labelRendererFactory,
        ImageExtensionFactory $imageExtensionFactory
    ) {
        $this->labelRendererFactory = $labelRendererFactory;
        $this->imageExtensionFactory = $imageExtensionFactory;
    }

    /**
     * @inheritdoc
     */
    public function collect(ProductInterface $product, ProductRenderInterface $productRender)
    {
        foreach ($productRender->getImages() as $image) {
            $extensionAttributes = $image->getExtensionAttributes();
            if (!$extensionAttributes) {
                $extensionAttributes = $this->imageExtensionFactory->create();
            }
            $extensionAttributes->setAwOnsaleLabel($this->getLabelHtml($product, $image->getCode()));
            $image->setExtensionAttributes($extensionAttributes);
        }
    }

    /**
     * Get onsale label html
     *
     * @param ProductInterface $product
     * @param string $image
     * @return string
     */
    private function getLabelHtml($product, $image)
    {
        /** @var LabelRenderer $labelRenderer */
        $labelRenderer = $this->labelRendererFactory->create(
            [
                'data' => [
                    'image' => $image,
                    'product' => $product
                ]
            ]
        );

        return $labelRenderer->toHtml();
    }
}
