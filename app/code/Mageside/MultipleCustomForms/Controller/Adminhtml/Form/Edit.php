<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Controller\Adminhtml\Form;

/**
 * Class Edit
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageside_MultipleCustomForms::mageside_multiple_custom_forms';

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomFormFactory
     */
    protected $_customFormFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomFormFactory
     */
    protected $_customFormResourceFactory;

    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mageside\MultipleCustomForms\Model\CustomFormFactory $customFormFactory
     * @param \Mageside\MultipleCustomForms\Model\ResourceModel\CustomFormFactory $customFormResourceFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageside\MultipleCustomForms\Model\CustomFormFactory $customFormFactory,
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomFormFactory $customFormResourceFactory
    ) {
        $this->_customFormFactory = $customFormFactory;
        $this->_customFormResourceFactory = $customFormResourceFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        if ($id = $this->getRequest()->getParam('id')) {
            $model = $this->_customFormFactory->create();
            $resourceModel = $this->_customFormResourceFactory->create();
            $resourceModel->load($model, $id);

            $resultPage->getConfig()->getTitle()->prepend(__('Edit Form'));
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('Form id: %id no longer exists.', ['id' => $id]));
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Form'));
        }

        return $resultPage;
    }
}
