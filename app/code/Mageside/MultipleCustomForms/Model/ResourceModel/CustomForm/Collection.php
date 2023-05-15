<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            \Mageside\MultipleCustomForms\Model\CustomForm::class,
            \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm::class
        );
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        /** @var \Mageside\MultipleCustomForms\Model\ResourceModel\CustomForm $resource */
        $resource = $this->getResource();
        foreach ($this->_items as $item) {
            $resource->loadAdditionalSettings($item);
            $resource->loadEmailsSettings($item);
        }

        return parent::_afterLoad();
    }
}
