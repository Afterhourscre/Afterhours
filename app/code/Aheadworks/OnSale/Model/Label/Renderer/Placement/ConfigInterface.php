<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Renderer\Placement;

/**
 * Interface ConfigInterface
 *
 * @package Aheadworks\OnSale\Model\Label\Renderer\Placement
 */
interface ConfigInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const MOVE_TO_SELECTOR_BY_AREA = 'move_to_selector_by_area';
    const IN_PARENT_SELECTOR = 'in_parent_selector';
    const SIZE = 'size';
    /**#@-*/

    /**
     * Get move to selector by area
     *
     * @return array
     */
    public function getMoveToSelectorByArea();

    /**
     * Get in parent selector
     *
     * @return string
     */
    public function getInParentSelector();

    /**
     * Get label size
     *
     * @return string
     */
    public function getSize();
}
