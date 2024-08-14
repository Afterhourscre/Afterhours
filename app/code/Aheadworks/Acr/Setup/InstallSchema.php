<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Aheadworks\Acr\Setup
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

        /**
         * Create table 'aw_acr_rule'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_acr_rule'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                'subject',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Subject'
            )
            ->addColumn(
                'content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Content'
            )
            ->addColumn(
                'email_send_days',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Email Send Days'
            )
            ->addColumn(
                'email_send_hours',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Email Send Hours'
            )
            ->addColumn(
                'email_send_minutes',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Email Send Minutes'
            )
            ->addColumn(
                'product_type_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Type IDs'
            )
            ->addColumn(
                'cart_conditions',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Cart Conditions Serialized'
            )
            ->addColumn(
                'product_conditions',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Product Conditions Serialized'
            )
            ->addColumn(
                'customer_groups',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Groups'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                'Status'
            )
            ->addIndex(
                $installer->getIdxName('aw_acr_queue', 'status'),
                'status'
            )
            ->setComment('AW Abandoned Cart Reminder Rule Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_acr_rule_store'
         */
        $table = $installer->getConnection()
        ->newTable($installer->getTable('aw_acr_rule_store'))
        ->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule ID'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )
        ->addIndex(
            $installer->getIdxName('aw_acr_rule_store', ['store_id']),
            ['store_id']
        )
        ->addForeignKey(
            $installer->getFkName('aw_acr_rule_store', 'rule_id', 'aw_acr_rule', 'id'),
            'rule_id',
            $installer->getTable('aw_acr_rule'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $installer->getFkName('aw_acr_rule_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->setComment(
            'AW ACR Rule To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_acr_rule_coupon'
         */
        $ruleTable = $installer->getConnection()->newTable($installer->getTable('aw_acr_rule_coupon'))
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'sales_rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'primary' => true],
                'Sales rule ID'
            )
            ->addColumn(
                'is_active',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is active'
            )
            ->addColumn(
                'code_length',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Code Length'
            )
            ->addColumn(
                'code_format',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['nullable' => false],
                'Code Format'
            )
            ->addColumn(
                'code_prefix',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['nullable' => true],
                'Code Prefix'
            )
            ->addColumn(
                'code_suffix',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['nullable' => true],
                'Code Suffix'
            )
            ->addColumn(
                'code_dash',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['nullable' => true],
                'Dash Every X Characters'
            )
            ->addForeignKey(
                $installer->getFkName('aw_acr_rule_coupon', 'rule_id', 'aw_acr_rule', 'id'),
                'rule_id',
                $installer->getTable('aw_acr_rule'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_acr_rule_coupon', 'sales_rule_id', 'salesrule', 'rule_id'),
                'sales_rule_id',
                $installer->getTable('salesrule'),
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment(
                'AW ACR Rule To Salesrule Linkage Table'
            );
        $installer->getConnection()->createTable($ruleTable);

        /**
         * Create table 'aw_acr_cart_history'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_acr_cart_history'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Event History Id'
            )
            ->addColumn(
                'reference_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Reference Id'
            )
            ->addColumn(
                'cart_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Cart Data'
            )
            ->addColumn(
                'triggered_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Triggered At'
            )
            ->addColumn(
                'processed',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Processed'
            )
            ->setComment('AW Abandoned Cart Reminder Cart History Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_acr_queue'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_acr_queue'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Queue Id'
            )
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule Id'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Status'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'scheduled_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Scheduled At'
            )
            ->addColumn(
                'sent_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Sent At'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->addColumn(
                'recipient_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Recipient Name'
            )
            ->addColumn(
                'recipient_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Recipient Email'
            )
            ->addColumn(
                'cart_history_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Cart History Id'
            )
            ->addColumn(
                'saved_subject',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Saved Subject'
            )
            ->addColumn(
                'saved_content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '1M',
                [],
                'Saved Content'
            )
            ->addIndex(
                $installer->getIdxName('aw_acr_queue', 'rule_id'),
                'rule_id'
            )
            ->addIndex(
                $installer->getIdxName('aw_acr_queue', 'status'),
                'status'
            )
            ->addForeignKey(
                $installer->getFkName('aw_acr_queue', 'rule_id', 'aw_acr_rule', 'id'),
                'rule_id',
                $installer->getTable('aw_acr_rule'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_acr_queue', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('AW Abandoned Cart Reminder Queue Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
