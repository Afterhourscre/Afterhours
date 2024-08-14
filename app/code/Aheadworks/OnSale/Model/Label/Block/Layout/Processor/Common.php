<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Layout\Processor;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Model\Source\Label\Type;

/**
 * Class Common
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Layout\Processor
 */
class Common implements LayoutProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $labelBlock, $scope)
    {
        $label = $labelBlock->getLabel();
        $jsLayout['components'] = [
            $scope => [
                'component' => 'Aheadworks_OnSale/js/ui/components/label',
                'text' => $labelBlock->getLabelText($labelBlock->getLabelSize()),
                'textConfig' => ['variableValues' => $labelBlock->getLabelTextVariableValues()],
                'labelType' => $label->getType(),
                'shapeType' => $this->prepareShapeType($label),
                'customizeCssLabel' => $label->getCustomizeCssLabel($labelBlock->getLabelSize()),
                'customizeCssContainer' => $label->getCustomizeCssContainer($labelBlock->getLabelSize())
            ]
        ];

        return $jsLayout;
    }

    /**
     * Prepare shape type
     *
     * @param LabelInterface $label
     * @return string
     */
    private function prepareShapeType($label)
    {
        $shapeType = '';
        if ($label->getType() == Type::SHAPE) {
            $shapeType = $label->getShapeType();
        }

        return $shapeType;
    }
}
