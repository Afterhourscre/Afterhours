<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Adminhtml\Edit;

class SaveDependency extends GenericButton
{
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'primary',
            'data_attribute' => [
                'mage-init' => [
                    'Mageside_MultipleCustomForms/js/components/form/button' => []
                ]
            ],
            'on_click' => ''
        ];
    }
}
