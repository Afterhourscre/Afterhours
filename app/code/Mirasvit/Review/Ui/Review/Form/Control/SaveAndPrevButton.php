<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-review
 * @version   1.1.2
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Review\Ui\Review\Form\Control;


class SaveAndPrevButton extends ButtonAbstract
{
    public function getButtonData()
    {
        $prevPreview = $this->getPrevReview();

        if (!$prevPreview) {
            return [];
        }

        return [
            'label' => __('Save and Previous'),
            'class' => 'next',
            'on_click' => '',
            'sort_order' => 60,
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'mst_review_form.mst_review_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    ['proceed' => $prevPreview->getId()],
                                ]
                            ]
                        ]
                    ]
                ],

            ]
        ];
    }
}
