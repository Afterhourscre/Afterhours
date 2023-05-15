<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Layout\Processor;

use Aheadworks\OnSale\Api\Data\BlockInterface;

/**
 * Interface LayoutProcessorInterface
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Layout\Processor
 */
interface LayoutProcessorInterface
{
    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @param BlockInterface $labelBlock
     * @param string $scope
     * @return array
     */
    public function process($jsLayout, $labelBlock, $scope);
}
