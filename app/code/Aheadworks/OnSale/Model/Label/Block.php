<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label;

use Aheadworks\OnSale\Api\Data\BlockInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Block
 *
 * @package Aheadworks\OnSale\Model\Label
 */
class Block extends AbstractExtensibleObject implements BlockInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelTextLarge()
    {
        return $this->_get(self::LABEL_TEXT_LARGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelTextLarge($labelTextLarge)
    {
        return $this->setData(self::LABEL_TEXT, $labelTextLarge);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelTextMedium()
    {
        return $this->_get(self::LABEL_TEXT_MEDIUM);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelTextMedium($labelTextMedium)
    {
        return $this->setData(self::LABEL_TEXT_MEDIUM, $labelTextMedium);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelTextSmall()
    {
        return $this->_get(self::LABEL_TEXT_SMALL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelTextSmall($labelTextSmall)
    {
        return $this->setData(self::LABEL_TEXT_MEDIUM, $labelTextSmall);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelSize()
    {
        return $this->_get(self::LABEL_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelSize($labelSize)
    {
        return $this->setData(self::LABEL_SIZE, $labelSize);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelText($labelText)
    {
        return $this->setData(self::LABEL_TEXT, $labelText);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelText($size = null)
    {
        return $size
            ? $this->_get(self::LABEL_TEXT . '_' . $size)
            : $this->_get(self::LABEL_TEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelTextVariableValues()
    {
        return $this->_get(self::LABEL_TEXT_VARIABLE_VALUES);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelTextVariableValues($labelTextVariableValues)
    {
        return $this->setData(self::LABEL_TEXT_VARIABLE_VALUES, $labelTextVariableValues);
    }
}
