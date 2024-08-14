<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 05.11.18
 * Time: 10:46
 */

namespace MageCloud\SalesOrder\Model\Resource;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SalesOrderAttachment extends AbstractDb {

    protected function _construct() {
        $this->_init('sales_order_item_attach', 'id');
    }
}