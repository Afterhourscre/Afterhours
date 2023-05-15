<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Model;

/**
 * Class Recipient
 * @package Mageside\MultipleCustomForms\Model
 */
class Recipient extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Mageside\MultipleCustomForms\Model\ResourceModel\Recipient::class);
    }
}
