<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Controller\Adminhtml\Fieldset;

/**
 * Class Save
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageside_MultipleCustomForms::mageside_multiple_custom_forms';

    /**
     * @var array
     */
    protected $_models = [];

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomFormFactory
     */
    protected $_customFormFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\FieldsetFactory
     */
    protected $_fieldsetFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Mageside\MultipleCustomForms\Model\CustomFormFactory $customFormFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Mageside\MultipleCustomForms\Model\CustomForm\FieldsetFactory $fieldsetFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Mageside\MultipleCustomForms\Model\CustomFormFactory $customFormFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Mageside\MultipleCustomForms\Model\CustomForm\FieldsetFactory $fieldsetFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_resultForwardFactory    = $resultForwardFactory;
        $this->_customFormFactory       = $customFormFactory;
        $this->_fieldsetFactory         = $fieldsetFactory;
        $this->_resultJsonFactory       = $resultJsonFactory;
        $this->_layoutFactory           = $layoutFactory;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__('Invalid form data.'));

            return $resultRedirect->setPath('*/*/manage');
        }

        $requestData = $this->getRequest()->getPostValue();
        /** @var \Mageside\MultipleCustomForms\Model\CustomForm\Fieldset $model */
        $model = $this->_fieldsetFactory->create();

        try {
            if (isset($requestData['fieldset'])) {
                if (isset($requestData['fieldset']['store_id'])) {
                    $storeId = (int) $requestData['fieldset']['store_id'];
                    $store = $this->storeManager->getStore($storeId);
                    $this->storeManager->setCurrentStore($store->getCode());
                }
                if (isset($requestData['fieldset']['id']) && ($fieldsetId = $requestData['fieldset']['id'])) {
                    $model->load($fieldsetId);
                }
                if (isset($requestData['fieldset']['form_id'])) {
                    $model->addData($requestData['fieldset']);
                    if (!empty($requestData['use_default'])) {
                        foreach ($requestData['use_default'] as $name => $value) {
                            if ($value === "1") {
                                $model->unsetData($name);
                            }
                        }
                    }
                    $model->save();
                    $this->messageManager->addSuccessMessage(__('You saved the fieldset.'));
                } else {
                    $this->messageManager->addErrorMessage(__('Please save form before continue.'));
                }
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving fieldset.'));
        }

        $hasError = (bool)$this->messageManager->getMessages()->getCountByType(
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );

        /** @var $block \Magento\Framework\View\Element\Messages */
        $block = $this->_layoutFactory->create()->getMessagesBlock();
        $block->setMessages($this->messageManager->getMessages(true));

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->_resultJsonFactory->create();

        $data = [];
        if ($model) {
            /** @var \Mageside\MultipleCustomForms\Model\CustomForm\Field $model */
            $data = [
                'id'        => $model->getId(),
                'name'      => $model->getName(),
                'title'     => $model->getTitle(),
                'position'  => $model->getPosition(),
            ];
        }

        return $resultJson->setData(
            [
                'messages'  => $block->getGroupedHtml(),
                'error'     => $hasError,
                'record'    => $data
            ]
        );
    }
}
