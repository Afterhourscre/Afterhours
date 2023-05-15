<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Block\Adminhtml\Edit;

use Magento\Ui\Component\Control\Container;

class Save extends GenericButton
{
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save form',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'custom_form.custom_form',
                                'actionName' => 'save',
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => Container::SPLIT_BUTTON,
            'sort_order' => 100,
            'options' => $this->getOptions(),
        ];
    }

    protected function getOptions()
    {
        $options[] = [
            'id_hard' => 'save',
            'label' => __('Save'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'custom_form.custom_form',
                                'actionName' => 'save'
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $options[] = [
            'id_hard' => 'save_and_continue',
            'label' => __('Save And Continue'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'custom_form.custom_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'continue'
                                    ]
                                ]
                            ],
                        ]
                    ]
                ]
            ],
        ];

        return $options;
    }
}
