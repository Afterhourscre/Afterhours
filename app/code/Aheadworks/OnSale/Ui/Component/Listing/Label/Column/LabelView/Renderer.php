<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\Component\Listing\Label\Column\LabelView;

use Aheadworks\OnSale\Block\Adminhtml\Label\Renderer\Label as BackendLabel;
use Aheadworks\OnSale\Ui\Component\Listing\Label\Column\LabelView\Renderer\LabelResolver;
use Magento\Framework\View\LayoutFactory;
use Aheadworks\OnSale\Model\Label\Block\Factory as BlockFactory;

/**
 * Class Renderer
 *
 * @package Aheadworks\OnSale\Ui\Component\Listing\Label\Column\LabelView
 */
class Renderer
{
    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var LabelResolver
     */
    private $labelResolver;

    /**
     * @param LayoutFactory $layoutFactory
     * @param BlockFactory $blockFactory
     * @param LabelResolver $labelResolver
     */
    public function __construct(
        LayoutFactory $layoutFactory,
        BlockFactory $blockFactory,
        LabelResolver $labelResolver
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->blockFactory = $blockFactory;
        $this->labelResolver = $labelResolver;
    }

    /**
     * Prepare label data for rendering in column
     *
     * @param array|int $label
     * @param string $labelText
     * @return string
     */
    public function render($label, $labelText)
    {
        $labelModel = $this->labelResolver->resolve($label);
        $labelBlock = $this->blockFactory->createForTest($labelModel, $labelText);

        /** @var BackendLabel $backendLabelBlock */
        $backendLabelBlock = $this->layoutFactory->create()->createBlock(BackendLabel::class);
        $backendLabelBlock
            ->setLabelBlock($labelBlock);

        return $backendLabelBlock->toHtml();
    }
}
