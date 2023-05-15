<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface VariableProcessorInterface
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor
 */
interface VariableProcessorInterface
{
    /**
     * Process the label text variable
     *
     * @param ProductInterface $product
     * @param array $params
     * @return mixed
     */
    public function process($product, $params);

    /**
     * Process the label text variable for test
     *
     * @param array $params
     * @return mixed
     */
    public function processTest($params);
}
