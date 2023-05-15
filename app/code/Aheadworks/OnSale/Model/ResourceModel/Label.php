<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel as MagentoFrameworkAbstractModel;
use Aheadworks\OnSale\Api\Data\LabelInterface;

/**
 * Class Label
 *
 * @package Aheadworks\OnSale\Model\ResourceModel
 */
class Label extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_onsale_label';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, LabelInterface::LABEL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function save(MagentoFrameworkAbstractModel $object)
    {
        $object->beforeSave();
        return parent::save($object);
    }

    /**
     * {@inheritdoc}
     */
    public function load(MagentoFrameworkAbstractModel $object, $objectId, $field = null)
    {
        if (!empty($objectId)) {
            $arguments = $this->getArgumentsForEntity();
            $this->entityManager->load($object, $objectId, $arguments);
            $object->afterLoad();
        }
        return $this;
    }
}
