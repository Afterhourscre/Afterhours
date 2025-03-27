<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Block\Label\Renderer;

use Aheadworks\OnSale\Api\Data\BlockInterface;
use Aheadworks\OnSale\Model\Label\Block\Layout\Processor\LayoutProcessorInterface;
use Aheadworks\OnSale\Model\Label\Renderer\ConfigMetadata;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Label
 *
 * @method Label setLabelBlock(BlockInterface $labelBlock)
 * @method BlockInterface getLabelBlock()
 * @method Label setLabelConfig(ConfigMetadata $labelConfig)
 * @method ConfigMetadata getLabelConfig()
 * @package Aheadworks\OnSale\Block\Label\Renderer
 */
class Label extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_OnSale::label/renderer/label.phtml';

    /**
     * @var LayoutProcessorInterface[]
     */
    private $layoutProcessors;

    /**
     * @var string
     */
    private $uId;

    /**
     * @param Context $context
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layoutProcessors = $layoutProcessors;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];
        $this->uId = uniqid();
    }

    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout, $this->getLabelBlock(), $this->getScope());
        }

        return json_encode($this->jsLayout);
    }

    /**
     * Retrieve data role
     *
     * @return string
     */
    public function getDataRole()
    {
        return 'aw-onsale-label-' . $this->uId;
    }

    /**
     * Retrieve scope
     *
     * @return string
     */
    public function getScope()
    {
        return 'awOnSaleLabel' . $this->uId;
    }
}
