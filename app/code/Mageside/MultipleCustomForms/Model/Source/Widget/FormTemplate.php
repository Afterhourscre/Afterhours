<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\Source\Widget;

class FormTemplate implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'static', 'label' => __('Static'), 'to_preview_menu' => true],
            ['value' => 'popup', 'label' => __('Popup'), 'to_preview_menu' => false],
            ['value' => 'popup_with_button', 'label' => __('Popup with button'), 'to_preview_menu' => true],
        ];
    }
}
