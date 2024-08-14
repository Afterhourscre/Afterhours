<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\Component\Form\Rule\Element;

use Aheadworks\OnSale\Model\Source\Label\Text\Variable;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Container;

/**
 * Class FrontendLabelNotice
 *
 * @package Aheadworks\OnSale\Ui\Component\Form\Rule\Element
 */
class FrontendLabelNotice extends Container
{
    /**
     * @var Variable
     */
    private $labelTextVariable;

    /**
     * @param ContextInterface $context
     * @param Variable $labelTextVariable
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Variable $labelTextVariable,
        $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->labelTextVariable = $labelTextVariable;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $config['tooltip']['description'] =
            $this->labelTextVariable->getOptionsAsVariableDescription($this->labelTextVariable->toOptionArray());
        $this->setData('config', (array)$config);
    }
}
