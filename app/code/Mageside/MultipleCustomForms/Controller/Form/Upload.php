<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Controller\Form;

use Magento\Framework\Controller\ResultFactory;
use Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings;

class Upload extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\FileUploader
     */
    protected $_fileUploader;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\FieldFactory
     */
    protected $_fieldFactory;

    /**
     * Upload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Mageside\MultipleCustomForms\Model\FileUploader $fileUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Mageside\MultipleCustomForms\Model\FileUploader $fileUploader,
        \Mageside\MultipleCustomForms\Model\CustomForm\FieldFactory $fieldFactory
    ) {
        $this->_fileUploader = $fileUploader;
        $this->_fieldFactory = $fieldFactory;
        parent::__construct($context);
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            if ($fieldId = $this->getRequest()->getParam('field_id')) {
                $field = $this->_fieldFactory->create()->load($fieldId);
                if ($field->getId()) {
                    if ($extensions = $field->getData(Settings::OPTION_ALLOWED_EXTENSIONS)) {
                        $allowedExtensions = explode(',', $extensions);
                        array_walk(
                            $allowedExtensions,
                            function (&$value) {
                                $value = strtolower(trim($value));
                            }
                        );
                        $this->_fileUploader->setAllowedExtensions($allowedExtensions);
                    }
                }
            }
            $result = $this->_fileUploader->saveFileToTmpDir('files');
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
