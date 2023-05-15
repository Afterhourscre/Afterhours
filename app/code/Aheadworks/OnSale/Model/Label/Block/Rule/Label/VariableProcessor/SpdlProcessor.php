<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor;

use Magento\Framework\Stdlib\DateTime;

/**
 * Class SpdlProcessor
 *
 * @package Aheadworks\OnSale\Model\Label\Block\Rule\Label\VariableProcessor
 */
class SpdlProcessor implements VariableProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($product, $params)
    {
        $specialPriceToDate = $product->getSpecialToDate();

        return $specialPriceToDate ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        $currentDate = new \DateTime('today');
        $currentDate->modify('+2 day');

        return $currentDate->format(DateTime::DATETIME_INTERNAL_FORMAT);
    }
}
