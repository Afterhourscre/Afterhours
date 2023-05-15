<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm\Field;

class Options extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('ms_cf_field_options', 'id');
    }

    /**
     * Save options
     *
     * @param $data
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveOptions($data)
    {
        //$this->getConnection()->insertMultiple($this->getMainTable(), $data);
        foreach ($data as $row) {
            $this->getConnection()->insert($this->getMainTable(), ['field_id' => $row['field_id']]);
            $id = $this->getConnection()->lastInsertId($this->getMainTable());
            $this->getConnection()->insert(
                $this->getTable('ms_cf_field_options_label'),
                [
                    'option_id' => $id,
                    'label'     => $row['label'],
                    'store_id'  => $row['store_id'],
                ]
            );
        }

        return $this;
    }

    /**
     * Get options by ids
     *
     * @param array $ids
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOptionsByIds(array $ids)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['main_table' => $this->getMainTable()], ['label'])
            ->where("id in (?)", $ids);

        $result = $connection->fetchAll($select);

        return $result;
    }

    /**
     * Update options
     *
     * @param $options
     * @return $this
     */
    public function updateOptions($options)
    {
        $connection = $this->getConnection();

        $ids = [];
        $storeId = 0;
        foreach ($options as $option) {
            $ids[] = $option['option_id'];
            $storeId = $option['store_id'];
        }

        if (!empty($ids)) {
            $connection->delete(
                $this->getTable('ms_cf_field_options_label'),
                ['option_id IN (?)' => $ids, 'store_id = ?' => $storeId]
            );
            $this->getConnection()->insertMultiple($this->getTable('ms_cf_field_options_label'), $options);
        }

        return $this;
    }

    /**
     * Delete options by ids
     *
     * @param $ids
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteOptions($ids)
    {
        $connection = $this->getConnection();
        $connection->delete($this->getMainTable(), ['id IN (?)' => $ids]);

        return $this;
    }
}
