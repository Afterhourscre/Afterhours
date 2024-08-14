<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Controller\Adminhtml\Form\Submission;

class Manage extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageside_MultipleCustomForms::mageside_multiple_custom_forms';

    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create('page');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Submissions'));

        return $resultPage;
    }
}
