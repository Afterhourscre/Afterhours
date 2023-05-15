<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Api\Data;

/**
 * Label text value interface
 * @api
 */
interface LabelTextInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const VALUE_LARGE = 'value_large';
    const VALUE_MEDIUM = 'value_medium';
    const VALUE_SMALL = 'value_small';
    /**#@-*/

    /**
     * Get option value large
     *
     * @return string
     */
    public function getValueLarge();

    /**
     * Set option value large
     *
     * @param string $valueLarge
     * @return $this
     */
    public function setValueLarge($valueLarge);

    /**
     * Get option value medium
     *
     * @return string
     */
    public function getValueMedium();

    /**
     * Set option value medium
     *
     * @param string $valueMedium
     * @return $this
     */
    public function setValueMedium($valueMedium);

    /**
     * Get option value small
     *
     * @return string
     */
    public function getValueSmall();

    /**
     * Set option value small
     *
     * @param string $valueSmall
     * @return $this
     */
    public function setValueSmall($valueSmall);
}
