<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block;

use Aheadworks\OnSale\Api\Data\BlockInterface;
use Aheadworks\OnSale\Api\Data\BlockInterfaceFactory;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct\RuleProductInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class Factory
 *
 * @package Aheadworks\OnSale\Model\Label\Block
 */
class Factory
{
    /**
     * @var VariableProcessor
     */
    private $labelTextVariableProcessor;

    /**
     * @var BlockInterfaceFactory
     */
    private $blockFactory;

    /**
     * @param VariableProcessor $labelTextVariableProcessor
     * @param BlockInterfaceFactory $blockFactory
     */
    public function __construct(
        VariableProcessor $labelTextVariableProcessor,
        BlockInterfaceFactory $blockFactory
    ) {
        $this->labelTextVariableProcessor = $labelTextVariableProcessor;
        $this->blockFactory = $blockFactory;
    }

    /**
     * Create label block
     *
     * @param LabelInterface $label
     * @param array $labelTexts
     * @param ProductInterface $product
     * @return BlockInterface
     */
    public function create($label, $labelTexts, $product)
    {
        return $this->createBlock($label, $labelTexts, $product, false);
    }

    /**
     * Create label block for test
     *
     * @param LabelInterface $label
     * @param string $labelText
     * @return BlockInterface
     */
    public function createForTest($label, $labelText)
    {
        $labelTexts = [
            RuleProductInterface::LABEL_TEXT_LARGE => $labelText,
            RuleProductInterface::LABEL_TEXT_MEDIUM => $labelText,
            RuleProductInterface::LABEL_TEXT_SMALL => $labelText
        ];
        $labelBlock = $this->createBlock($label, $labelTexts, null, true);
        $labelBlock->setLabelSize('large');
        return $labelBlock;
    }

    /**
     * Create label block
     *
     * @param LabelInterface $label
     * @param string|array $labelTextData
     * @param ProductInterface $product
     * @param bool $forTest
     * @return BlockInterface
     */
    private function createBlock($label, $labelTextData, $product, $forTest)
    {
        /** @var BlockInterface $blockModel */
        $blockModel = $this->blockFactory->create();
        $blockModel->setLabel($label);

        $variableValues = [];
        if (is_array($labelTextData)) {
            foreach ($labelTextData as $labelTextType => $labelText) {
                $blockModel->setData($labelTextType, $labelText);
                $variableValue = $forTest
                    ? $this->labelTextVariableProcessor->processVariableInLabelTestText($labelText)
                    : $this->labelTextVariableProcessor->processVariableInLabelText($product, $labelText);
                $variableValues = array_merge($variableValues, $variableValue);
            }
        }

        $blockModel->setLabelTextVariableValues($variableValues);
        return $blockModel;
    }
}
