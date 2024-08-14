<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 26.10.17
 * Time: 12:21
 */
namespace MageCloud\SalesOrder\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 * @package MageCloud\SalesOrder\Setup
 */

class InstallSchema implements InstallSchemaInterface{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {

        $installer = $setup;

        $installer->startSetup();

        $tableName = $installer->getTable('sales_order_item_attach');

        if (!$installer->getConnection()->isTableExists($tableName)) {

            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null, [
                            'identity' => true,
                            'unsigned' => true,
                            'nullable' => false,
                            'primary' => true,
                            'auto_increment' => true
                        ], 'ID')
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null, [
                            'unsigned' => true,
                            'nullable' => false
                        ], 'Order ID')
                ->addColumn(
                    'order_item_id',
                    Table::TYPE_INTEGER,
                    null, [
                            'unsigned' => true,
                            'nullable' => false
                        ], 'Order Item ID')
                ->addColumn(
                    'image_name',
                    Table::TYPE_TEXT,
                    null, [
                    'nullable' => true,
                    'text' => true,
                    'default' => null
                ], 'Image Name')
                ->addColumn(
                    'image_path',
                    Table::TYPE_TEXT,
                    null, [
                    'nullable' => true,
                    'image' => true,
                    'default' => null
                ], 'Image Path')
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    null, [
                            'nullable' => true,
                            'fulltext' => true,
                            'default' => null
                        ], 'Description')
                ->addColumn(
                    'create_time',
                    Table::TYPE_TIMESTAMP,
                    null, [
                            'nullable' => true,
                            'default' => Table::TIMESTAMP_INIT
                        ], 'Create Time')
                ->setComment('Sales Order Item Attachment Table');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
?>