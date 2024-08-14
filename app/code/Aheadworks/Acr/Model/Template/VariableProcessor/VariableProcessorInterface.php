<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Template\VariableProcessor;

/**
 * Interface VariableProcessorInterface
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
interface VariableProcessorInterface
{
    /**
     * @param QuoteData $quote
     * @param array $params
     * @return array
     */
    public function process($quote, $params);

    /**
     * @param array $params
     * @return array
     */
    public function processTest($params);
}
