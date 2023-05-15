<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Controller\Adminhtml\Form;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageside_MultipleCustomForms::mageside_multiple_custom_forms';

    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomFormFactory
     */
    protected $_customFormFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomFormFactory
     */
    protected $formFactory;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mageside\MultipleCustomForms\Model\CustomFormFactory $customFormFactory
     * @param \Mageside\MultipleCustomForms\Model\CustomFormFactory $formFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageside\MultipleCustomForms\Model\CustomFormFactory $customFormFactory,
        \Mageside\MultipleCustomForms\Model\CustomFormFactory $formFactory
    ) {
        $this->_customFormFactory = $customFormFactory;
        $this->formFactory = $formFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__('Invalid form data.'));

            return $resultRedirect->setPath('*/*/manage');
        }

        $requestData = $this->getRequest()->getPostValue();
        $model = $this->formFactory->create();

        try {
            if (isset($requestData['form'])) {
                if (!empty($requestData['use_default'])) {
                    foreach ($requestData['use_default'] as $name => $value) {
                        if ($value === "1") {
                            unset($requestData['form'][$name]);
                        }
                    }
                }
                $model->addData($requestData['form']);
                $model->save();
                $this->messageManager->addSuccessMessage(__('Form saved successfully.'));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving form.'));
        }

        if (isset($requestData['back']) && $model->getId()) {
            $params = ['id' => $model->getId()];
            if ($model->getStoreId() && $model->getStoreId() != '0') {
                $params['store'] = $model->getStoreId();
            }
            return $resultRedirect->setPath('customform/form/edit', $params);
        }

        return $resultRedirect->setPath('*/*/manage');
    }
}
