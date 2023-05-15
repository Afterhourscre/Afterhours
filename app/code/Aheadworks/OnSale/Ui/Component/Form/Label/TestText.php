<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\Component\Form\Label;

use Aheadworks\OnSale\Model\Source\Label\Text\Variable;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class TestText
 *
 * @package Aheadworks\OnSale\Ui\Component\Form\Label
 */
class TestText extends Field
{
    /**
     * @var Variable
     */
    private $labelTextVariable;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Variable $labelTextVariable
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Variable $labelTextVariable,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->uiComponentFactory = $uiComponentFactory;
        $this->labelTextVariable = $labelTextVariable;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $config['tooltip']['description'] = $this->labelTextVariable->getOptionsAsVariableDescription(
            $this->labelTextVariable->getVariablesAvailableInTestArea()
        );
        $this->setData('config', (array)$config);
    }
}
