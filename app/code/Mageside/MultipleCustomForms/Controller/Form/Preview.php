<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Controller\Form;

/**
 * Class Preview
 * @package Mageside\MultipleCustomForms\Controller\Form
 */
class Preview extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create('page');
        if ($formId = $this->getRequest()->getParam("id")) {
            if ($block = $resultPage->getLayout()->getBlock('custom_form_preview')) {
                $block->setFormId($formId);
                if ($formDisplay = $this->getRequest()->getParam('form_display', 'static')) {
                    $block->setFormDisplay($formDisplay);
                }
            }
        }

        return $resultPage;
    }
}
