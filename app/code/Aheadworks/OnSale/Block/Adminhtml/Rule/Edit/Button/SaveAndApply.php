<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Block\Adminhtml\Rule\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveAndApply
 *
 * @package Aheadworks\OnSale\Block\Adminhtml\Rule\Edit\Button
 */
class SaveAndApply extends AbstractButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Apply'),
            'class' => 'save',
            'on_click' => '',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'aw_onsale_rule_form.aw_onsale_rule_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    ['auto_apply' => 1],
                                ]
                            ]
                        ]
                    ]
                ],
                'form-role' => 'save'
            ],
            'sort_order' => 40
        ];
    }
}
