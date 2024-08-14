<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel;

/**
 * Class Recipient
 * @package Mageside\MultipleCustomForms\Model\ResourceModel
 */
class Recipient extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings
     */
    protected $fieldSettings;

    /**
     * @var CustomForm\Field\CollectionFactory
     */
    protected $fieldCollectionFactory;

    protected function _construct()
    {
        $this->_init('ms_cf_recipient', 'id');
    }

    /**
     * Recipient constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings
     * @param CustomForm\Field\CollectionFactory $fieldCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Mageside\MultipleCustomForms\Model\CustomForm\Field\Settings $fieldSettings,
        \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field\CollectionFactory $fieldCollectionFactory
    ) {
        $this->fieldSettings = $fieldSettings;
        $this->fieldCollectionFactory = $fieldCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param $emails
     * @param $form
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insertNewEmails($emails, $form)
    {
        $formId = $form->getId();
        $connection = $this->getConnection();
        foreach ($emails as $item) {
            $connection->insert(
                $this->getMainTable(),
                [
                    'form_id'   => $formId,
                    'emails'    => $item['recipient_emails'],
                    'store_id'  => $item['store_id']
                ]
            );
            $item['id'] = $connection->lastInsertId($this->getMainTable());
            $this->saveDependency($item, $form);
        }

        return $this;
    }

    /**
     * @param $emails
     * @param $form
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateEmails($emails, $form)
    {
        $formId = $form->getId();
        $connection = $this->getConnection();
        foreach ($emails as $item) {
            $connection->update(
                $this->getMainTable(),
                [
                    'form_id'   => $formId,
                    'emails'    => $item['recipient_emails']
                ],
                ['id = ?'       => (int)$item['id']]
            );
            $this->saveDependency($item, $form);
        }

        return $this;
    }

    /**
     * @param $email
     * @param $form
     * @return $this
     */
    public function saveDependency($email, $form)
    {
        if (!$form->getEmails()) {
            return $this;
        }
        $dependency = [];
        $parseDependency = false;
        if (!empty($email['dependency'])) {
            if (is_string($email['dependency'])) {
                $parseDependency = json_decode($email['dependency'], TRUE);
            } else {
                $parseDependency = $email['dependency'];
            }
        }
        if ($parseDependency) {
            foreach ($form->getFields() as $field) {
                if (!$this->fieldSettings->hasOptionsData($field['type'])) {
                    continue;
                }
                $fieldDependency = 'field_' . $field['id'];
                if (!empty($parseDependency[$fieldDependency])) {
                    $value = implode(",", $parseDependency[$fieldDependency]);
                    $dependency[] = [
                        'recipient_id'  => $email['id'],
                        'field_id'      => $field['id'],
                        'value'         => $value,
                    ];
                }
            }

            $connection = $this->getConnection();
            $connection->delete(
                $this->getTable('ms_cf_recipient_dependency'),
                ['recipient_id = ?' => $email['id']]
            );
            if (!empty($dependency)) {
                $connection->insertMultiple(
                    $this->getTable('ms_cf_recipient_dependency'),
                    $dependency
                );
            }
        }

        return $this;
    }

    /**
     * @param $ids
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteEmails($ids)
    {
        $connection = $this->getConnection();
        $connection->delete($this->getMainTable(), ['id IN (?)' => $ids]);

        return $this;
    }

    /**
     * @param $formFields
     * @param $formId
     * @param $data
     * @param $storeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEmailsByDependency($formFields, $formId, $data, $storeId)
    {
        $connection = $this->getConnection();

        $stores = [0];
        if ($storeId != 0) {
            $stores = [$storeId, 0];
        }

        foreach ($stores as $store) {
            $selectInner = $connection->select()
                ->from(['main_table' => $this->getMainTable()])
                ->where('main_table.form_id = ?', (int) $formId)
                ->where('main_table.store_id = ?', (int) $store);
            $test = $connection->fetchOne($selectInner);
            if (!empty($test)) {
                break;
            }
        }

        foreach ($formFields as $filter) {
            if (!$this->fieldSettings->hasOptionsData($filter['type'])) {
                continue;
            }
            $cond = $connection->quoteInto("rd{$filter['id']}.field_id = ?", (int) $filter['id']);
            $selectInner->join(
                ["rd{$filter['id']}" => $this->getTable('ms_cf_recipient_dependency')],
                "main_table.id = rd{$filter['id']}.recipient_id AND {$cond}",
                ["field_{$filter['id']}" => "rd{$filter['id']}.value"]
            );
        }

        $where = $this->prepareWhereConditions($formFields, $data);
        $selectOuter = $connection->select()
            ->from($selectInner, ['emails'])
            ->where($where);

        return $connection->fetchAll($selectOuter);
    }

    /**
     * @param $formId
     * @param $storeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCommonEmails($formId, $storeId)
    {
        $connection = $this->getConnection();

        $stores = [0];
        if ($storeId != 0) {
            $stores = [$storeId, 0];
        }

        foreach ($stores as $store) {
            $select = $connection->select()
                ->from(['main_table' => $this->getMainTable()], ['emails'])
                ->where('main_table.form_id = ?', $formId)
                ->where('main_table.store_id = ?', (int) $store)
                ->group('emails');
            $test = $connection->fetchOne($select);
            if (!empty($test)) {
                break;
            }
        }

        $selectExclude = $connection->select()
            ->from(['main_table' => $this->getMainTable()], ['id'])
            ->join(
                ["dep" => $this->getTable('ms_cf_recipient_dependency')],
                "main_table.id = dep.recipient_id",
                null
            )
            ->where('main_table.form_id = ?', $formId)
            ->where('main_table.store_id = ?', $store)
            ->group('id');
        $depIds = $data = $connection->fetchCol($selectExclude);

        if (!empty($depIds)) {
            $select->where('main_table.id NOT IN (?)', $depIds);
        }

        return $connection->fetchAll($select);
    }

    /**
     * @param $fields
     * @param $data
     * @return string
     */
    private function prepareWhereConditions($fields, $data)
    {
        $array = [];
        foreach ($fields as $field) {
            if (!$this->fieldSettings->hasOptionsData($field['type'])) {
                continue;
            }
            $this->addToArray($array, $field['id'], $data['field_'.$field['id']]);
        }

        $cond = [];
        foreach ($array as $row) {
            $subCond = [];
            foreach ($row as $key => $value) {
                $subCond[] = $this->getConnection()->quoteInto("FIND_IN_SET(?, `field_{$key}`)", $value);
            }
            $cond[] = '(' . implode(' AND ', $subCond) . ')';
        }
        if (empty($cond)) {
            $cond = ['1 = 1'];
        }
        $where = implode(' OR ', $cond);

        return $where;
    }

    /**
     * @param $array
     * @param $fieldId
     * @param $value
     */
    private function addToArray(&$array, $fieldId, $value)
    {
        if (empty($array)) {
            $array[] = [];
        }

        if (is_array($value)) {
            $newArray = [];
            foreach ($array as $row) {
                foreach ($value as $item) {
                    $row[$fieldId] = $item;
                    $newArray[] = $row;
                }
            }
            $array = $newArray;
        } else {
            foreach ($array as $index => $row) {
                $array[$index][$fieldId] = $value;
            }
        }
    }

    /**
     * @param $formId
     * @param $store
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadEmails($formId, $store)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['main_table' => $this->getMainTable()])
            ->where('main_table.form_id = ?', (int) $formId)
            ->where('main_table.store_id = ?', (int) $store);

        $result = $connection->fetchAll($select);
        if (!empty($result)) {
            $ids = [];
            foreach ($result as $row) {
                $ids[] = $row['id'];
            }
            $selectDep = $connection->select()
                ->from(['main_table' => $this->getTable('ms_cf_recipient_dependency')])
                ->where('main_table.recipient_id IN (?)', $ids);
            $dependencies = $connection->fetchAll($selectDep);
            $depData = [];
            foreach ($dependencies as $dep) {
                $depData[$dep['recipient_id']][] = $dep;
            }
            foreach ($result as $key => $item) {
                if (!empty($depData[$item['id']])) {
                    $result[$key]['dependency'] = $depData[$item['id']];
                }
            }
        }

        return $result;
    }
}
