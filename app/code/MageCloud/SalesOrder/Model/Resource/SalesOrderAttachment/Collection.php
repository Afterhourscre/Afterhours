<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 05.11.18
 * Time: 10:48
 */

namespace MageCloud\SalesOrder\Model\Resource\SalesOrderAttachment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MageCloud\SalesOrder\Model\SalesOrder;
use MageCloud\SalesOrder\Model\Resource\SalesOrderAttachment;

class Collection extends AbstractCollection
{
    /**
     * Define module
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            SalesOrder::class,
            SalesOrderAttachment::class
        );
    }
}