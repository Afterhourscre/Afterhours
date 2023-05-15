<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel\RecipientDependency;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Mageside\MultipleCustomForms\Model\RecipientDependency::class,
            \Mageside\MultipleCustomForms\Model\ResourceModel\RecipientDependency::class
        );
    }
}
