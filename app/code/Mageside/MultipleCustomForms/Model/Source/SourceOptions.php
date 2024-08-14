<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\Source;

class SourceOptions implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            0 => [
                'label' => '-- Please Select --',
                'value' => 0
            ],
            'country' => [
                'label' => 'Country',
                'value' => 'country'
            ],
            'region' => [
                'label' => 'Region',
                'value' => 'region'
            ],
            'product_attribute' => [
                'label' => 'Product Attribute',
                'value' => 'product_attribute'
            ],
            'custom' => [
                'label' => 'Custom',
                'value' => 'custom'
            ],
        ];

        return $options;
    }
}
