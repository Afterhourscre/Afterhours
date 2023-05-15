<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Coupongenerator\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Aheadworks\Coupongenerator\Setup
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
         * Create table 'aw_coupongenerator_salesrule'
         */

        /** @var \Magento\Framework\DB\Ddl\Table $ruleTable */
        $ruleTable = $installer->getConnection()->newTable($installer->getTable('aw_coupongenerator_salesrule'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Rule ID'
            )
            ->addColumn(
                'expiration_days',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Expiration Days'
            )
            ->addColumn(
                'coupon_length',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Coupon Length'
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
                $installer->getFkName('aw_coupongenerator_salesrule', 'rule_id', 'salesrule', 'rule_id'),
                'rule_id',
                $installer->getTable('salesrule'),
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
        ;
        $installer->getConnection()->createTable($ruleTable);

        /**
         * Create table 'aw_coupongenerator_coupon'
         */

        /** @var \Magento\Framework\DB\Ddl\Table $couponTable */
        $couponTable = $installer->getConnection()->newTable($installer->getTable('aw_coupongenerator_coupon'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'coupon_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true],
                'Coupon ID'
            )
            ->addColumn(
                'is_deactivated',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Is Deactivated'
            )
            ->addColumn(
                'admin_user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Created By'
            )
            ->addColumn(
                'recipient_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => true],
                'Recipient Email'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Customer Id'
            )
            ->addForeignKey(
                $installer->getFkName('aw_coupongenerator_coupon', 'coupon_id', 'salesrule_coupon', 'coupon_id'),
                'coupon_id',
                $installer->getTable('salesrule_coupon'),
                'coupon_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
        ;
        $installer->getConnection()->createTable($couponTable);

        $installer->endSetup();
    }
}
