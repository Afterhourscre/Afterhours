<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\OnSale\Model\ResourceModel\Rule;
use Aheadworks\OnSale\Model\ResourceModel\Label;
use Aheadworks\OnSale\Setup\Updater\Schema\Updater as SchemaUpdater;

/**
 * Class InstallSchema
 *
 * @package Aheadworks\OnSale\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var SchemaUpdater
     */
    private $updater;

    /**
     * @param SchemaUpdater $updater
     */
    public function __construct(
        SchemaUpdater $updater
    ) {
        $this->updater = $updater;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->createLabelTable($installer)
            ->createRuleTable($installer)
            ->createRuleWebsiteTable($installer)
            ->createRuleFrontendLabelTextTable($installer)
            ->createRuleProductTable($installer)
            ->createRuleProductIdxTable($installer);

        $this->updater->update110($setup);

        $installer->endSetup();
    }

    /**
     * Create table 'aw_onsale_label'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createLabelTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable(Label::MAIN_TABLE_NAME))
            ->addColumn(
                'label_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Label ID'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )->addColumn(
                'type',
                Table::TYPE_TEXT,
                80,
                ['nullable' => false],
                'Type'
            )->addColumn(
                'position',
                Table::TYPE_TEXT,
                80,
                ['nullable' => false],
                'Position'
            )->addColumn(
                'shape_type',
                Table::TYPE_TEXT,
                80,
                ['nullable' => true],
                'Shape Type'
            )->addColumn(
                'img_file',
                Table::TYPE_TEXT,
                150,
                ['nullable' => true],
                'Image File Path'
            )->addColumn(
                'customize_css_container',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => false],
                'Customize CSS Container'
            )->addColumn(
                'customize_css_label',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => false],
                'Customize CSS Label'
            )->setComment('AW OnSale Rule Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_onsale_rule'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createRuleTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable(Rule::MAIN_TABLE_NAME))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Rule ID'
            )->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Is Rule Active'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )->addColumn(
                'from_date',
                Table::TYPE_DATE,
                null,
                [],
                'Rule is Active From'
            )->addColumn(
                'to_date',
                Table::TYPE_DATE,
                null,
                [],
                'Rule is Active To'
            )->addColumn(
                'priority',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false],
                'Priority'
            )->addColumn(
                'label_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Label ID'
            )->addColumn(
                'customer_groups',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => false],
                'Customer Groups'
            )->addColumn(
                'product_condition',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Product Condition'
            )->addIndex(
                $setup->getIdxName(Rule::MAIN_TABLE_NAME, ['is_active', 'priority', 'to_date', 'from_date']),
                ['is_active', 'priority', 'to_date', 'from_date']
            )->addForeignKey(
                $setup->getFkName(Rule::MAIN_TABLE_NAME, 'label_id', Label::MAIN_TABLE_NAME, 'label_id'),
                'label_id',
                $setup->getTable(Label::MAIN_TABLE_NAME),
                'label_id',
                Table::ACTION_SET_NULL
            )->setComment('AW OnSale Rule Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_onsale_rule_website'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createRuleWebsiteTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable(Rule::WEBSITE_TABLE_NAME))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Rule ID'
            )->addColumn(
                'website_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Website ID'
            )->addIndex(
                $setup->getIdxName(Rule::WEBSITE_TABLE_NAME, ['rule_id']),
                ['rule_id']
            )->addIndex(
                $setup->getIdxName(Rule::WEBSITE_TABLE_NAME, ['website_id']),
                ['website_id']
            )->addForeignKey(
                $setup->getFkName(Rule::WEBSITE_TABLE_NAME, 'rule_id', Rule::MAIN_TABLE_NAME, 'rule_id'),
                'rule_id',
                $setup->getTable(Rule::MAIN_TABLE_NAME),
                'rule_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(Rule::WEBSITE_TABLE_NAME, 'website_id', 'store_website', 'website_id'),
                'website_id',
                $setup->getTable('store_website'),
                'website_id',
                Table::ACTION_CASCADE
            )->setComment('AW OnSale Rule To Website Relation Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_onsale_rule_frontend_text'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createRuleFrontendLabelTextTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable(Rule::FRONTEND_LABEL_TEXT_TABLE_NAME))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Value'
            )->addIndex(
                $setup->getIdxName(Rule::FRONTEND_LABEL_TEXT_TABLE_NAME, ['rule_id']),
                ['rule_id']
            )->addIndex(
                $setup->getIdxName(Rule::FRONTEND_LABEL_TEXT_TABLE_NAME, ['store_id']),
                ['store_id']
            )->addIndex(
                $setup->getIdxName(Rule::FRONTEND_LABEL_TEXT_TABLE_NAME, ['value']),
                ['value']
            )->addForeignKey(
                $setup->getFkName(
                    Rule::FRONTEND_LABEL_TEXT_TABLE_NAME,
                    'rule_id',
                    Rule::MAIN_TABLE_NAME,
                    'rule_id'
                ),
                'rule_id',
                $setup->getTable(Rule::MAIN_TABLE_NAME),
                'rule_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(Rule::FRONTEND_LABEL_TEXT_TABLE_NAME, 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment(
                'AW OnSale Rule Frontend Text Table'
            );
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_onsale_rule_product'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createRuleProductTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable(Rule::PRODUCT_TABLE_NAME))
            ->addColumn(
                'rule_product_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Product Id'
            ) ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Rule Id'
            )->addColumn(
                'from_date',
                Table::TYPE_DATE,
                null,
                [],
                'Rule is Active From'
            )->addColumn(
                'to_date',
                Table::TYPE_DATE,
                null,
                [],
                'Rule is Active To'
            )->addColumn(
                'customer_group_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Customer Group Id'
            )->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Product Id'
            )->addColumn(
                'priority',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Priority'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store Id'
            )->addColumn(
                'label_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Label ID'
            )->addColumn(
                'label_text',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Label Text'
            )->addIndex(
                $setup->getIdxName(
                    Rule::PRODUCT_TABLE_NAME,
                    ['rule_id', 'from_date', 'to_date', 'store_id', 'customer_group_id', 'product_id', 'priority'],
                    true
                ),
                ['rule_id', 'from_date', 'to_date', 'store_id', 'customer_group_id', 'product_id', 'priority'],
                ['type' => 'unique']
            )
            ->addIndex(
                $setup->getIdxName(Rule::PRODUCT_TABLE_NAME, ['customer_group_id']),
                ['customer_group_id']
            )
            ->addIndex(
                $setup->getIdxName(Rule::PRODUCT_TABLE_NAME, ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName(Rule::PRODUCT_TABLE_NAME, ['from_date']),
                ['from_date']
            )
            ->addIndex(
                $setup->getIdxName(Rule::PRODUCT_TABLE_NAME, ['to_date']),
                ['to_date']
            )
            ->addIndex(
                $setup->getIdxName(Rule::PRODUCT_TABLE_NAME, ['product_id']),
                ['product_id']
            )
            ->setComment('AW OnSale Rule Product');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Create table 'aw_onsale_rule_product_idx'
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createRuleProductIdxTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable($setup->getTable(Rule::PRODUCT_IDX_TABLE_NAME))
            ->addColumn(
                'rule_product_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Product Id'
            ) ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Rule Id'
            )->addColumn(
                'from_date',
                Table::TYPE_DATE,
                null,
                [],
                'Rule is Active From'
            )->addColumn(
                'to_date',
                Table::TYPE_DATE,
                null,
                [],
                'Rule is Active To'
            )->addColumn(
                'customer_group_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Customer Group Id'
            )->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Product Id'
            )->addColumn(
                'priority',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Priority'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store Id'
            )->addColumn(
                'label_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Label ID'
            )->addColumn(
                'label_text',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Label Text'
            )->addIndex(
                $setup->getIdxName(
                    Rule::PRODUCT_IDX_TABLE_NAME,
                    ['rule_id', 'from_date', 'to_date', 'store_id', 'customer_group_id', 'product_id', 'priority'],
                    true
                ),
                ['rule_id', 'from_date', 'to_date', 'store_id', 'customer_group_id', 'product_id', 'priority'],
                ['type' => 'unique']
            )
            ->addIndex(
                $setup->getIdxName(Rule::PRODUCT_IDX_TABLE_NAME, ['customer_group_id']),
                ['customer_group_id']
            )
            ->addIndex(
                $setup->getIdxName(Rule::PRODUCT_IDX_TABLE_NAME, ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName(Rule::PRODUCT_IDX_TABLE_NAME, ['from_date']),
                ['from_date']
            )
            ->addIndex(
                $setup->getIdxName(Rule::PRODUCT_IDX_TABLE_NAME, ['to_date']),
                ['to_date']
            )
            ->addIndex(
                $setup->getIdxName(Rule::PRODUCT_IDX_TABLE_NAME, ['product_id']),
                ['product_id']
            )
            ->setComment('AW OnSale Rule Product Idx');
        $setup->getConnection()->createTable($table);

        return $this;
    }
}
