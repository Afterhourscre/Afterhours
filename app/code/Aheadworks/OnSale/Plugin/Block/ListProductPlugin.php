<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Plugin\Block;

use Aheadworks\OnSale\Block\Label\Renderer as LabelRenderer;
use Aheadworks\OnSale\Block\Label\RendererFactory as LabelRendererFactory;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Placement;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Size;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Model\Product;

/**
 * Class ListProductPlugin
 *
 * @package Aheadworks\OnSale\Plugin\Block
 */
class ListProductPlugin
{
    /**
     * @var LabelRendererFactory
     */
    private $labelRendererFactory;

    /**
     * @param LabelRendererFactory $labelRendererFactory
     */
    public function __construct(LabelRendererFactory $labelRendererFactory)
    {
        $this->labelRendererFactory = $labelRendererFactory;
    }

    /**
     * Render label if product valid
     *
     * @param AbstractProduct $subject
     * @param \Closure $proceed
     * @param Product $product
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetProductDetailsHtml($subject, $proceed, $product)
    {
        $html = $proceed($product);

        /** @var LabelRenderer $labelRenderer */
        $labelRenderer = $this->labelRendererFactory->create(
            [
                'data' => [
                    'placement' => Placement::PRODUCT_LIST,
                    'product' => $product
                ]
            ]
        );
        $html .= $labelRenderer->toHtml();

        return $html;
    }
}
