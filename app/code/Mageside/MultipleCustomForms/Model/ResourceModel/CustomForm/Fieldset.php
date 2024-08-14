<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm;

class Fieldset extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Class constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('ms_cf_fieldset', 'id');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setData(
            'name',
            strtolower(str_replace(' ', '-', trim($object->getData('name'))))
        );

        return parent::_beforeSave($object);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getStoreId() === null) {
            $object->setStoreId($this->storeManager->getStore()->getId());
        }
        $this->saveAdditionalSettings($object);

        return parent::_afterSave($object);
    }

    /**
     * @param $fieldset
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateFieldsetPosition($fieldset)
    {
        $connection = $this->getConnection();
        $connection->update(
            $this->getMainTable(),
            ['position' => (int)$fieldset['position']],
            ['id = ?' => (int)$fieldset['id']]
        );

        return $this;
    }

    /**
     * @param $ids
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteFieldsetsById($ids)
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
            $this->getTable('ms_cf_fieldset_settings'),
            [
                'fieldset_id = ?'   => $object->getId(),
                'store_id = ?'      => $storeId
            ]
        );

        if ($object->getTitle() !== null) {
            $connection->insert(
                $this->getTable('ms_cf_fieldset_settings'),
                [
                    'fieldset_id'   => $object->getId(),
                    'title'         => $object->getTitle(),
                    'store_id'      => $storeId
                ]
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
            ->from($this->getTable('ms_cf_fieldset_settings'))
            ->where('fieldset_id = ?', $object->getId());

        $data = $connection->fetchAll($select);
        $result = [];
        foreach ($data as $row) {
            $result[$row['store_id']] = $row;
        }

        $useDefault = $object->getData('useDefault') ? $object->getData('useDefault') : [];
        foreach ($stores as $store) {
            if (!empty($result[$store])) {
                foreach ($result[$store] as $key => $value) {
                    if (!empty($value)) {
                        $object->setData($key, $value);
                    }

                    if (in_array($key, ['title'])) {
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
}
