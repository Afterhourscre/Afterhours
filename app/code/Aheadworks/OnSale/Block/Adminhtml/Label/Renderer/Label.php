<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Block\Adminhtml\Label\Renderer;

use Aheadworks\OnSale\Block\Label\Renderer\Label as FrontendLabel;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\OnSale\Model\Label\Renderer\ConfigMetadataFactory;

/**
 * Class Label
 *
 * @package Aheadworks\OnSale\Block\Adminhtml\Label\Renderer
 */
class Label extends FrontendLabel
{
    /**
     * @var ConfigMetadataFactory
     */
    private $configMetadata;

    /**
     * @param Context $context
     * @param ConfigMetadataFactory $configMetadata
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigMetadataFactory $configMetadata,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $layoutProcessors, $data);
        $this->configMetadata = $configMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelConfig()
    {
        return $this->hasData('label_config')
            ? $this->getData('label_config')
            : $this->configMetadata->create();
    }
}
