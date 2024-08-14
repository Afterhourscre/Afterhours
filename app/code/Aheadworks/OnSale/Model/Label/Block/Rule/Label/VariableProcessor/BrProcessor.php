<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;

/**
 * Class BrProcessor
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor
 */
class BrProcessor implements VariableProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($product, $params)
    {
        return $this->getHtmlTag();
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return $this->getHtmlTag();
    }

    /**
     * Retrieve html tag
     *
     * @return string
     */
    private function getHtmlTag()
    {
        return '<br/>';
    }
}
