<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\Source;

class DateTimeType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Product date option type.
     */
    const OPTION_TYPE_DATE = 'date';

    /**
     * Product datetime option type.
     */
    const OPTION_TYPE_DATE_TIME = 'date_time';

    /**
     * Product time option type.
     */
    const OPTION_TYPE_TIME = 'time';

    public function toOptionArray()
    {
        $options = [
            0 => [
                'label' => __('-- Please Select --'),
                'value' => ''
            ],
            self::OPTION_TYPE_DATE => [
                'label' => __('Date'),
                'value' => self::OPTION_TYPE_DATE
            ],
            self::OPTION_TYPE_TIME => [
                'label' => __('Time'),
                'value' => self::OPTION_TYPE_TIME            ],
            self::OPTION_TYPE_DATE_TIME => [
                'label' => __('Date and Time'),
                'value' => self::OPTION_TYPE_DATE_TIME
            ],
        ];

        return $options;
    }
}
