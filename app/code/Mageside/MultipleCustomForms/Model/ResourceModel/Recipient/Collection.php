<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model\ResourceModel\Recipient;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Mageside\MultipleCustomForms\Model\Recipient::class,
            \Mageside\MultipleCustomForms\Model\ResourceModel\Recipient::class
        );
    }
}
