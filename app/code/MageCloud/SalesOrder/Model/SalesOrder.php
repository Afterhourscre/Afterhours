<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 05.11.18
 * Time: 10:43
 */

namespace MageCloud\SalesOrder\Model;

use Magento\Framework\Model\AbstractModel;

class SalesOrder extends AbstractModel
{


    protected function _construct() {
        $this->_init('MageCloud\SalesOrder\Model\Resource\SalesOrderAttachment');
    }
}