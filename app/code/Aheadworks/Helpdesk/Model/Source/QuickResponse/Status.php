<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model\Source\QuickResponse;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Aheadworks\Helpdesk\Model\Source\QuickResponse
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Quick response's statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/
    
    /**
     * Prepare array with options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_ENABLED,  'label' => __('Enabled')],
            ['value' => self::STATUS_DISABLED,  'label' => __('Disabled')],
        ];
    }

    /**
     * Prepare array with options for mass action
     *
     * @return array
     */
    public function toOptionArrayForMassStatus()
    {
        return [
            ['value' => self::STATUS_ENABLED,  'label' => __('Enable')],
            ['value' => self::STATUS_DISABLED,  'label' => __('Disable')],
        ];
    }
}
