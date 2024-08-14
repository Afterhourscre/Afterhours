<?php

namespace WeltPixel\AdvancedWishlist\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;


/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $tableName = 'wishlist';

        $installer->getConnection()->addColumn(
            $installer->getTable($tableName),
            'wishlist_name',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 32,
                'nullable' => true,
                'default' => 'My Wish List',
                'comment' => 'Wishlist Name'
            ]
        );

        $installer->getConnection()->dropForeignKey(
            $installer->getTable($tableName),
            $installer->getFkName('wishlist', 'customer_id', 'customer_entity', 'entity_id')
        );


        $installer->getConnection()->dropIndex(
            $installer->getTable($tableName),
            $installer->getIdxName(
                $tableName,
                'customer_id',
                AdapterInterface::INDEX_TYPE_UNIQUE
            )
        );

        $installer->getConnection()->addForeignKey(
            $installer->getFkName($tableName, 'customer_id', 'customer_entity', 'entity_id'),
            $installer->getTable($tableName),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $installer->getConnection()->addIndex(
            $installer->getTable($tableName),
            $installer->getIdxName(
                $tableName,
                'customer_id'
            ),
            'customer_id'
        );

        $installer->endSetup();
    }
}