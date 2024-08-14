<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\Source;

class InputType implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        $options = [
            0 => [
                'label' => 'Please select',
                'value' => 0
            ],
            1 => [
                'label' => 'Option 1',
                'value' => 1
            ],
            2  => [
                'label' => 'Option 2',
                'value' => 2
            ],
            3 => [
                'label' => 'Option 3',
                'value' => 3
            ],
        ];

        return $options;
    }
}
