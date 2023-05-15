<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel;

use Mageside\MultipleCustomForms\Model\CustomForm as CustomFormModel;
use Mageside\MultipleCustomForms\Model\FileUploaderFactory;
use Mageside\MultipleCustomForms\Model\CustomForm\Field;

class Submission extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var FileUploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Mageside\MultipleCustomForms\Model\FileUploader
     */
    protected $_fileUploader;

    protected $_fieldCollection = null;

    /**
     * Submission resource model constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        FileUploaderFactory $fileUploaderFactory,
        $connectionName = null
    ) {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('ms_cf_submission', 'id');
    }

    /**
     * @param \Mageside\MultipleCustomForms\Model\Submission $object
     * @return null|\Mageside\MultipleCustomForms\Model\CustomForm
     */
    protected function getCustomForm(\Mageside\MultipleCustomForms\Model\Submission $object)
    {
        return $object->getFormModel();
    }

    /**
     * @param \Mageside\MultipleCustomForms\Model\Submission $object
     * @return mixed
     */
    protected function getFieldCollection(\Mageside\MultipleCustomForms\Model\Submission $object)
    {
        if (!$this->_fieldCollection) {
            $this->_fieldCollection = $this->getCustomForm($object)->getFieldCollection();
        }

        return $this->_fieldCollection;
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $formId = $object->getFormId();
        $submissionId = $object->getId();
        /** @var \Mageside\MultipleCustomForms\Model\CustomForm\Field $field */
        foreach ($this->getFieldCollection($object) as $field) {
            if (!$value = $field->getSubmittedValue($object->getData())) {
                continue;
            }
            $value = $this->prepareValue($value, $field, $object);
            $table = $this->getMainTable() . '_' . $field->getBackendType();
            $data = [
                'submission_id' => $submissionId,
                'form_id'       => $formId,
                'field_id'      => $field->getId(),
                'value'         => $value
            ];
            $this->getConnection()->insert($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * @param $value
     * @param $field
     * @param $object
     * @return array
     */
    protected function prepareValue($value, $field, $object)
    {
        if ($field->getType() == 'file') {
            $value = $this->moveUploadedFiles($value);
        }

        if (is_array($value)) {
            $value = implode(',', $value);
            $object->setData(Field::FIELD_PREFIX . $field->getId(), htmlspecialchars($value, ENT_QUOTES));
        }

        return htmlspecialchars($value, ENT_QUOTES);
    }

    /**
     * @param $files
     * @return array
     */
    protected function moveUploadedFiles($files)
    {
        $files = explode(',', $files);
        if (!$this->_fileUploader) {
            $this->_fileUploader = $this->_fileUploaderFactory->create();
        }
        $newFiles = [];
        foreach ($files as $file) {
            $newFiles[] = $this->_fileUploader->moveFileFromTmp($file);
        }

        return $newFiles;
    }
}
