<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\Source;

use Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings;

class HiddenOptions implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        $options = [
            0 => [
                'label' => '-- Please Select --',
                'value' => 0
            ],
            Settings::OPTION_HIDDEN_STATIC => [
                'label' => 'Static Value',
                'value' => Settings::OPTION_HIDDEN_STATIC
            ],
            Settings::OPTION_HIDDEN_PRODUCT_ATTRIBUTE => [
                'label' => 'Product Attribute',
                'value' => Settings::OPTION_HIDDEN_PRODUCT_ATTRIBUTE            ],
            Settings::OPTION_HIDDEN_CATEGORY_ATTRIBUTE => [
                'label' => 'Category Attribute',
                'value' => Settings::OPTION_HIDDEN_CATEGORY_ATTRIBUTE
            ],
            Settings::OPTION_HIDDEN_CUSTOMER_ATTRIBUTE => [
                'label' => 'Customer Attribute',
                'value' => Settings::OPTION_HIDDEN_CUSTOMER_ATTRIBUTE
            ],
        ];

        return $options;
    }
}
