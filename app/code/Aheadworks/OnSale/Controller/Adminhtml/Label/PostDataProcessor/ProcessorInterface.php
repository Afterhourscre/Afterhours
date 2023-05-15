<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Label\PostDataProcessor;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Label\PostDataProcessor
 */
interface ProcessorInterface
{
    /**
     * Prepare entity data for save
     *
     * @param array $data
     * @return array
     */
    public function process($data);
}
