<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Plugin\Block\Product;

use Aheadworks\OnSale\Block\Label\Renderer as LabelRenderer;
use Aheadworks\OnSale\Block\Label\RendererFactory as LabelRendererFactory;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Placement;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Size;
use Magento\Catalog\Block\Product\AbstractProduct;

/**
 * Class ImagePlugin
 *
 * @package Aheadworks\OnSale\Plugin\Block\Product
 */
class ImagePlugin
{
    /**
     * @var LabelRendererFactory
     */
    private $labelRendererFactory;

    /**
     * @param LabelRendererFactory $labelRendererFactory
     */
    public function __construct(
        LabelRendererFactory $labelRendererFactory
    ) {
        $this->labelRendererFactory = $labelRendererFactory;
    }

    /**
     * Render label if product is valid
     *
     * @param AbstractProduct $subject
     * @param string $resultHtml
     * @return string
     */
    public function afterToHtml($subject, $resultHtml)
    {
        $product = $subject->getProduct();
        $imageId = $subject->getImageId();

        if ($product && $imageId) {
            /** @var LabelRenderer $labelRenderer */
            $labelRenderer = $this->labelRendererFactory->create(
                [
                    'data' => [
                        'image' => $imageId,
                        'product' => $product
                    ]
                ]
            );
            $resultHtml .= $labelRenderer->toHtml();
        }

        return $resultHtml;
    }
}
