<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface BlockInterface
 * @api
 */
interface BlockInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const LABEL = 'label';
    const LABEL_TEXT = 'label_text';
    const LABEL_TEXT_LARGE = 'label_text_large';
    const LABEL_TEXT_MEDIUM = 'label_text_medium';
    const LABEL_TEXT_SMALL = 'label_text_small';
    const LABEL_TEXT_VARIABLE_VALUES = 'label_text_variable_values';
    const LABEL_SIZE = 'label_size';
    /**#@-*/

    /**
     * Get label
     *
     * @return \Aheadworks\OnSale\Api\Data\LabelInterface
     */
    public function getLabel();

    /**
     * Set label
     *
     * @param \Aheadworks\OnSale\Api\Data\LabelInterface $label
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get label large
     *
     * @return string
     */
    public function getLabelTextLarge();

    /**
     * Set label large
     *
     * @param string $labelTextLarge
     * @return $this
     */
    public function setLabelTextLarge($labelTextLarge);

    /**
     * Get label medium
     *
     * @return string
     */
    public function getLabelTextMedium();

    /**
     * Set label medium
     *
     * @param string $labelTextMedium
     * @return $this
     */
    public function setLabelTextMedium($labelTextMedium);

    /**
     * Get label small
     *
     * @return string
     */
    public function getLabelTextSmall();

    /**
     * Set label small
     *
     * @param string $labelTextSmall
     * @return $this
     */
    public function setLabelTextSmall($labelTextSmall);

    /**
     * Get label text variable values
     *
     * @return string[]
     */
    public function getLabelTextVariableValues();

    /**
     * Set label text variable values
     *
     * @param string[] $labelTextVariableValues
     * @return $this
     */
    public function setLabelTextVariableValues($labelTextVariableValues);

    /**
     * Set label text
     *
     * @param string $labelTextSmall
     * @return string
     */
    public function setLabelText($labelTextSmall);

    /**
     * Get label text
     *
     * @return string
     */
    public function getLabelText($size = null);

    /**
     * Get label size
     *
     * @return string
     */
    public function getLabelSize();

    /**
     * Set label size
     *
     * @param string $labelSize
     * @return $this
     */
    public function setLabelSize($labelSize);
}
