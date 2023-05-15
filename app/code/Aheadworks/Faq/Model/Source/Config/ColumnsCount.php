<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Model\Source\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * FAQ Settings Column Options
 */
class ColumnsCount implements ArrayInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            for ($i = 1; $i <= 3; $i++) {
                $this->options[$i] = $i;
            }
        }
        return $this->options;
    }
}
