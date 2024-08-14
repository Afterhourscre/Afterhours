<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Block\Adminhtml\Label\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;

/**
 * Class Save
 *
 * @package Aheadworks\OnSale\Block\Adminhtml\Label\Edit\Button
 */
class Save extends AbstractButton implements ButtonProviderInterface
{
    /**
     * Target name to apply actions
     */
    const TARGET_NAME = 'aw_onsale_label_form.aw_onsale_label_form';

    /**
     * @inheritdoc
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => self::TARGET_NAME,
                                'actionName' => 'save',
                                'params' => [
                                    true
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getOptions(),
            'sort_order' => 50
        ];
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    private function getOptions()
    {
        $options = [];

        foreach ($this->getButtonOptionsData() as $buttonOptionsData) {
            $options[] = [
                'label' => $buttonOptionsData['label'],
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => self::TARGET_NAME,
                                    'actionName' => 'save',
                                    'params' => $buttonOptionsData['params']
                                ]
                            ]
                        ]
                    ]
                ],
            ];
        }

        return $options;
    }

    /**
     * Get button options data
     *
     * @return array
     */
    private function getButtonOptionsData()
    {
        return [
            [
                'label' => __('Save & Duplicate'),
                'params' => [
                    true,
                    [
                        'back' => 'duplicate'
                    ]
                ]
            ]
        ];
    }
}
