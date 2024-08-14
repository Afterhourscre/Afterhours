<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel;

class AttributeResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('eav_attribute', 'attribute_id');
    }

    /**
     * Get product attributes array
     *
     * @return array
     */
    public function getSelectTypeProductAttributes()
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                ['a' => $this->getMainTable()],
                ['a.attribute_id', 'a.attribute_code', 'a.frontend_label']
            )
            ->join(
                ['t' => $this->getTable('eav_entity_type')],
                'a.entity_type_id = t.entity_type_id',
                []
            )
            ->where('t.entity_type_code = ?', 'catalog_product')
            ->where('a.frontend_input = ?', 'select')
            ->where('a.is_user_defined = ?', '1')
            ->where('a.backend_type = ?', 'int');

        return $connection->fetchAll($select);
    }

    /**
     * @param $entity
     * @return array
     */
    public function getAttributesByEntity($entity)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                ['a' => $this->getMainTable()],
                ['a.attribute_id', 'a.attribute_code', 'a.frontend_label']
            )
            ->join(
                ['t' => $this->getTable('eav_entity_type')],
                'a.entity_type_id = t.entity_type_id',
                []
            )
            ->where('t.entity_type_code = ?', $entity)
            ->where('a.frontend_input NOT IN (?)', ['media_image', 'boolean']);

        return $connection->fetchAll($select);
    }

    /**
     * @return array
     */
    public function getProductAttributes()
    {
        return $this->getAttributesByEntity('catalog_product');
    }

    /**
     * @return array
     */
    public function getCustomerAttributes()
    {
        return $this->getAttributesByEntity('customer');
    }

    /**
     * @return array
     */
    public function getCategoryAttributes()
    {
        return $this->getAttributesByEntity('catalog_category');
    }
}
