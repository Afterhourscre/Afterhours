<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Cookie
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\Cookie\Setup;

use Extait\Cookie\Api\Data\CategoryInterface;
use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Model\ResourceModel\Category;
use Extait\Cookie\Model\ResourceModel\Cookie;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createCategoryTable($setup);
        $this->createCookieTable($setup);
        $this->addColumnToCustomerEntityTable($setup);

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function createCategoryTable(SchemaSetupInterface $setup)
    {
        $tableName = Category::MAIN_TABLE;
        if (!$setup->getConnection()->isTableExists($setup->getTable($tableName))) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable($tableName))
                ->addColumn(
                    CategoryInterface::ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Category ID'
                )->addColumn(
                    CategoryInterface::NAME,
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Category Name'
                )->addColumn(
                    CategoryInterface::DESCRIPTION,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Category Description'
                )->addColumn(
                    CategoryInterface::IS_SYSTEM,
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Category Is System'
                );

            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function createCookieTable(SchemaSetupInterface $setup)
    {
        $tableName = Cookie::MAIN_TABLE;
        if (!$setup->getConnection()->isTableExists($setup->getTable($tableName))) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable($tableName))
                ->addColumn(
                    CookieInterface::ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Cookie ID'
                )->addColumn(
                    CookieInterface::NAME,
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Cookie Name'
                )->addColumn(
                    CookieInterface::DESCRIPTION,
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Cookie Description'
                )->addColumn(
                    CookieInterface::CATEGORY_ID,
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true, 'unsigned' => true],
                    'Cookie Category ID'
                )->addColumn(
                    CategoryInterface::IS_SYSTEM,
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Cookie is system'
                )->addForeignKey(
                    $setup->getFkName(
                        $tableName,
                        CookieInterface::CATEGORY_ID,
                        Category::MAIN_TABLE,
                        CategoryInterface::ID
                    ),
                    CookieInterface::CATEGORY_ID,
                    $setup->getTable(Category::MAIN_TABLE),
                    CategoryInterface::ID,
                    Table::ACTION_SET_NULL
                );

            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addColumnToCustomerEntityTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('customer_entity'),
            'extait_cookie_categories',
            [
                'type' => Table::TYPE_TEXT,
                'length' => null,
                'nullable' => true,
                'comment' => 'Cookie Categories',
            ]
        );
    }
}
