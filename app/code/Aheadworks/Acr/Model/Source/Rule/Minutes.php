<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\Source\Rule;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Minutes
 * @package Aheadworks\Acr\Model\Source\Rule
 */
class Minutes implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $units = __('minutes');
            for ($minutes = 0; $minutes < 60; $minutes += 5) {
                $this->options[] = [
                    'value' => $minutes,
                    'label' => $minutes . ' ' . $units
                ];
            }
        }
        return $this->options;
    }
}
