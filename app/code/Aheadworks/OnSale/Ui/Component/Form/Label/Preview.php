<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Ui\Component\Form\Label;

use Magento\Ui\Component\Container;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\OnSale\Model\Source\Label\Position\Area as AreaSource;
use Aheadworks\OnSale\Model\Source\Label\Position as PositionSource;
use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;
use Aheadworks\OnSale\Model\Source\Label\Text\Variable;
use Aheadworks\OnSale\Model\Source\Label\Renderer\Size as LabelSize;

/**
 * Class Preview
 *
 * @package Aheadworks\OnSale\Ui\Component\Form\Label
 */
class Preview extends Container
{
    /**
     * @var AreaSource
     */
    private $areaSource;

    /**
     * @var PositionSource
     */
    private $positionSource;

    /**
     * @var Variable
     */
    private $labelTextVariable;

    /**
     * @var VariableProcessor
     */
    private $labelTextVariableProcessor;

    /**
     * @var LabelSize
     */
    private $labelSize;

    /**
     * @param ContextInterface $context
     * @param AreaSource $areaSource
     * @param PositionSource $positionSource
     * @param Variable $labelTextVariable
     * @param VariableProcessor $labelTextVariableProcessor
     * @param LabelSize $labelSize
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        AreaSource $areaSource,
        PositionSource $positionSource,
        Variable $labelTextVariable,
        VariableProcessor $labelTextVariableProcessor,
        LabelSize $labelSize,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->areaSource = $areaSource;
        $this->positionSource = $positionSource;
        $this->labelTextVariable = $labelTextVariable;
        $this->labelTextVariableProcessor = $labelTextVariableProcessor;
        $this->labelSize = $labelSize;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        $config['labelConfig']['textConfig']['variableValues'] =
            $this->labelTextVariableProcessor->processVariableInLabelTestText($this->generateLabelText());
        $config['areaMap'] = $this->areaSource->getPositionByAreaMap();
        $config['positionClassesMap'] = $this->positionSource->getPositionClassesMap();
        $config['sizeList'] = $this->labelSize->getSizeList();

        $this->setData('config', (array)$config);
    }

    /**
     * Generate label test from test variables
     *
     * @return string
     */
    private function generateLabelText()
    {
        $testLabelText = '';
        $options = $this->labelTextVariable
            ->getOptionsAsVariableDescription($this->labelTextVariable->getVariablesAvailableInTestArea());
        foreach ($options as $option) {
            $testLabelText .= $option['value'];
        }

        return $testLabelText;
    }
}
