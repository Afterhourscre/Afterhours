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
class RecipientDependency extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('ms_cf_recipient_dependency', 'id');
    }
}
