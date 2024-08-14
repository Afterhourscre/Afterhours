<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm;

use Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings;

class Field extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Options
     */
    protected $_optionsResourceModel;

    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings
     */
    protected $_fieldSettings;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    protected $_defaultMappingValues = [
        'title'         => null,
        'type'          => null,
        'placeholder'   => null,
        'default_value' => null,
        'position'      => 0,
        'required'      => 0,
        'validation'    => null,
    ];

    /**
     * Class constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\Options $optionsResourceModel,
        \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        $this->_optionsResourceModel = $optionsResourceModel;
        $this->_fieldSettings = $fieldSettings;
        $this->storeManager = $storeManager;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('ms_cf_field', 'id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (is_array($object->getValidation())) {
            $object->setValidation(implode(',', $object->getValidation()));
        }

        $type = $object->getType();
        if ($type == 'select' || $type == 'radio') {
            if ($defaultValue = $object->getData('default_value_select')) {
                $object->setDefaultValue($defaultValue);
            }
        } elseif ($type == 'checkbox' || $type == 'multiselect') {
            if ($defaultValue = $object->getData('default_value_multiselect')) {
                $object->setDefaultValue(implode(',', $defaultValue));
            }
        } elseif ($type == 'date') {
            if ($defaultValue = $object->getData('default_value_date')) {
                $object->setDefaultValue($defaultValue);
            }
        }

        $origData = $object->getData();
        foreach ($origData as $key => $value) {
            if (empty($value)) {
                unset($origData[$key]);
            }
        }

        $object->addData($this->_defaultMappingValues);
        $object->addData($origData);

        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getStoreId() === null) {
            $object->setStoreId($this->storeManager->getStore()->getId());
        }
        $this->loadAdditionalSettings($object);

        return parent::_afterLoad($object);
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getStoreId() === null) {
            $object->setStoreId($this->storeManager->getStore()->getId());
        }
        $this->saveRelatedOptions($object);
        $this->saveAdditionalSettings($object);

        return parent::_afterSave($object);
    }

    /**
     * @param $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveRelatedOptions($object)
    {
        if (!$this->_fieldSettings->hasOptionsData($object->getType()) ||
            $object->getData(Settings::OPTION_OPTIONS_SOURCE) != 'custom'
        ) {
            return $this;
        }

        $fieldId = $object->getId();
        $options = [];
        $optionsUpdate = [];
        $optionsDeleteIds = [];
        $optionsToSave = $object->getOptions(false, true);
        if (!empty($optionsToSave) && is_array($optionsToSave)) {
            foreach ($optionsToSave as $option) {
                if (!empty($option['delete']) && $option['delete'] === 'true') {
                    $optionsDeleteIds[] = $option['id'];
                } elseif (!empty($option['id'])) {
                    $optionsUpdate[] = [
                        'option_id' => $option['id'],
                        'label'     => $option['label'],
                        'store_id'  => $object->getStoreId()
                    ];
                } else {
                    $options[] = [
                        'field_id'  => $fieldId,
                        'label'     => $option['label'],
                        'store_id'  => $object->getStoreId()
                    ];
                }
            }
        }

        if (!empty($optionsDeleteIds)) {
            $this->_optionsResourceModel->deleteOptions($optionsDeleteIds);
        }

        if (!empty($optionsUpdate)) {
            $this->_optionsResourceModel->updateOptions($optionsUpdate);
        }

        if (!empty($options)) {
            $this->_optionsResourceModel->saveOptions($options);
        }

        return $this;
    }

    /**
     * @param $input
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateFieldPosition($input)
    {
        $connection = $this->getConnection();
        $connection->update(
            $this->getMainTable(),
            ['position' => (int)$input['position']],
            ['id = ?' => (int)$input['id']]
        );

        return $this;
    }

    /**
     * @param $ids
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteFieldsById($ids)
    {
        $connection = $this->getConnection();
        $connection->delete($this->getMainTable(), ['id IN (?)' => $ids]);

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    public function saveAdditionalSettings(\Magento\Framework\Model\AbstractModel $object)
    {
        $storeId = (int) $object->getStoreId();
        $connection = $this->getConnection();
        $connection->delete(
            $this->getTable('ms_cf_field_settings'),
            ['`field_id` = ?' => $object->getId(), '`store_id` = ?' => $storeId]
        );

        $data = [];
        $settings = $this->_fieldSettings->getAllOptions();
        foreach ($settings as $key) {
            if ($value = $object->getData($key)) {
                $data[] = [$object->getId(), $key, $value, $storeId];
            }
        }

        if (!empty($data)) {
            $connection->insertArray(
                $this->getTable('ms_cf_field_settings'),
                ['field_id', 'key', 'value', 'store_id'],
                $data
            );
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function loadAdditionalSettings(\Magento\Framework\Model\AbstractModel $object)
    {
        $storeId = (int) $object->getStoreId();
        $stores = [0];
        if ($storeId != 0) {
            $stores = [0, $storeId];
        }

        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('ms_cf_field_settings'))
            ->where('field_id = ?', $object->getId());

        $data = $connection->fetchAll($select);
        $result = [];
        foreach ($data as $row) {
            $result[$row['store_id']][$row['key']] = $row['value'];
        }

        $useDefault = $object->getData('useDefault') ? $object->getData('useDefault') : [];
        foreach ($stores as $store) {
            if (!empty($result[$store])) {
                foreach ($result[$store] as $key => $value) {
                    if (!empty($value)) {
                        $object->setData($key, $value);
                    }

                    if (!in_array($key, ['options_source'])) {
                        if (!empty($value) && $store != 0) {
                            $useDefault[$key] = false;
                        } else {
                            $useDefault[$key] = true;
                        }
                    }
                }
            }
        }
        $object->setData('useDefault', $useDefault);

        return $this;
    }

    /**
     * @param $submissionId
     * @return array|string
     */
    public function loadSubmissionData($submissionId, \Mageside\MultipleCustomForms\Model\CustomForm\Field $field)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('ms_cf_submission_' . $field->getBackendType()), 'value')
            ->where('submission_id = ?', $submissionId)
            ->where('field_id = ?', $field->getId())
            ->where('form_id = ?', $field->getFormId());

        return $connection->fetchOne($select);
    }
}
