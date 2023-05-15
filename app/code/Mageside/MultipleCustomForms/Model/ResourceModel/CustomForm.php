<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel;

class CustomForm extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field
     */
    protected $fieldResourceModel;

    /**
     * @var CustomForm\Fieldset
     */
    protected $fieldsetResourceModel;

    /**
     * @var RecipientFactory
     */
    protected $recipientResourceModelFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Class constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field $fieldResourceModel,
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Fieldset $fieldsetResourceModel,
        \Mageside\MultipleCustomForms\Model\ResourceModel\RecipientFactory $recipientResourceModelFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        $connectionName = null
    ) {
        $this->storeManager = $storeManager;
        $this->fieldResourceModel = $fieldResourceModel;
        $this->fieldsetResourceModel = $fieldsetResourceModel;
        $this->recipientResourceModelFactory = $recipientResourceModelFactory;
        $this->request = $request;
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('ms_custom_form', 'id');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $formCode = preg_replace(
            '/[^a-z0-9]+/',
            '_',
            strtolower(str_replace(' ', '_', trim($object->getCode())))
        );
        $object->setCode($formCode);

        return parent::_beforeSave($object);
    }

    /**
     * @inheritdoc
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->updateRelatedFieldsList($object);
        $this->updateRelatedFieldsetsList($object);
        $this->saveEmailFieldsList($object);
        $this->saveAdditionalSettings($object);

        return parent::_afterSave($object);
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->loadAdditionalSettings($object);
        $this->loadEmailsSettings($object);

        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateRelatedFieldsList(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getInputs()) {
            return $this;
        }

        $deleteIds = [];
        foreach ($object->getInputs() as $input) {
            if (isset($input['delete']) && $input['delete'] === 'true') {
                $deleteIds[] = $input['id'];
                continue;
            }
            $this->fieldResourceModel->updateFieldPosition($input);
        }

        if (!empty($deleteIds)) {
            $this->fieldResourceModel->deleteFieldsById($deleteIds);
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateRelatedFieldsetsList(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getFieldsets()) {
            return $this;
        }

        $deleteIds = [];
        foreach ($object->getFieldsets() as $fieldset) {
            if (isset($fieldset['delete']) && $fieldset['delete'] === 'true') {
                $deleteIds[] = $fieldset['id'];
                continue;
            }
            $this->fieldsetResourceModel->updateFieldsetPosition($fieldset);
        }

        if (!empty($deleteIds)) {
            $this->fieldsetResourceModel->deleteFieldsetsById($deleteIds);
        }

        return $this;
    }

    /**
     * @param $object
     * @return $this
     */
    private function saveEmailFieldsList(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getEmails()) {
            return $this;
        }

        $toInsert = [];
        $toUpdate = [];
        $toDeleteIds = [];
        foreach ($object->getEmails() as $email) {
            if (!isset($email['store_id'])) {
                $email['store_id'] = $object->getData('store_id') ? $object->getData('store_id') : 0;
            }
            if ($email['store_id'] != $object->getData('store_id')) {
                $email['store_id'] = $object->getData('store_id') ? $object->getData('store_id') : 0;
                unset($email['id']);
            }
            if (!empty($email['delete']) && $email['delete'] === 'true') {
                if (!empty($email['id'])) {
                    $toDeleteIds[] = $email['id'];
                }
            } elseif (!empty($email['id'])) {
                $toUpdate[] = $email;
            } else {
                $toInsert[] = $email;
            }
        }

        if (!empty($toInsert)) {
            $this->recipientResourceModelFactory->create()->insertNewEmails($toInsert, $object);
        }

        if (!empty($toUpdate)) {
            $this->recipientResourceModelFactory->create()->updateEmails($toUpdate, $object);
        }

        if (!empty($toDeleteIds)) {
            $this->recipientResourceModelFactory->create()->deleteEmails($toDeleteIds);
        }

        return $this;
    }

    /**
     * @param $object
     * @return $this
     */
    private function saveAdditionalSettings(\Magento\Framework\Model\AbstractModel $object)
    {
        $settings = [];
        $settings[] = [
            'form_id'           => $object->getData('id'),
            'name'              => $object->getData('name'),
            'button_text'       => $object->getData('button_text'),
            'subject_email'     => $object->getData('subject_email'),
            'redirect_url'      => $object->getData('redirect_url'),
            'success_message'   => $object->getData('success_message'),
            'fail_message'      => $object->getData('fail_message'),
            'description'       => $object->getData('description'),
            'store_id'          => $object->getData('store_id') ? $object->getData('store_id') : 0
        ];

        $connection = $this->getConnection();
        $connection->delete(
            $this->getTable('ms_custom_form_settings'),
            [
                'form_id = ?' => $object->getData('id'),
                'store_id = ?' => $object->getData('store_id') ? $object->getData('store_id') : 0
            ]
        );
        $connection->insertArray(
            $this->getTable('ms_custom_form_settings'),
            ['form_id', 'name', 'button_text', 'subject_email', 'redirect_url', 'success_message', 'fail_message', 'description', 'store_id'],
            $settings
        );

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function loadAdditionalSettings(\Magento\Framework\Model\AbstractModel $object)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $stores = [0];
        if ($storeId != 0) {
            $stores = [0, $storeId];
        }

        $result = $this->loadSettings($object);
        $useDefault = $object->getData('useDefault') ? $object->getData('useDefault') : [];
        foreach ($stores as $store) {
            if (!empty($result[$store])) {
                foreach ($result[$store] as $key => $value) {
                    if (!empty($value)) {
                        $object->setData($key, $value);
                    }
                    if (!in_array($key, ['form_id', 'store_id'])) {
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
        $object->setData('store_id', $storeId);

        return $this;
    }

    /**
     * @param $object
     * @return array
     */
    public function loadSettings($object)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('ms_custom_form_settings'))
            ->where('form_id = ?', $object->getData('id'));

        $data = $connection->fetchAll($select);
        $result = [];
        foreach ($data as $row) {
            $result[$row['store_id']] = $row;
        }

        return $result;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadEmailsSettings(\Magento\Framework\Model\AbstractModel $object)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $stores = [0];
        if ($storeId != 0) {
            $stores = [$storeId, 0];
        }

        $result = [];
        foreach ($stores as $store) {
            $result = $this->recipientResourceModelFactory->create()->loadEmails($object->getId(), $store);
            if (!empty($result)) {
                break;
            }
        }

        $emails = [];
        foreach ($result as $row) {
            $dependency = [];
            if (!empty($row['dependency'])) {
                foreach ($row['dependency'] as $dep) {
                    $dependency['field_' . $dep['field_id']][] = $dep['value'];
                }
            }
            $emails[] = [
                'id'                => $row['id'],
                'recipient_emails'  => $row['emails'],
                'dependency'        => json_encode($dependency),
                'store_id'          => $row['store_id']
            ];

        }
        $object->setData('emails', $emails);

        return $this;
    }
}
