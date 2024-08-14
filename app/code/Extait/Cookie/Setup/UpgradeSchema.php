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
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            // Create the extait_cookie_category_store table and migrate data from the extait_cookie_category table.
            $this->createCategoryStoreTable($setup);
            $this->migrateCategoryData($setup);
            $this->dropCategoryColumns($setup);

            // Create the extait_cookie_cookie_store table and migrate data from the extait_cookie_cookie table.
            $this->createCookieStoreTable($setup);
            $this->migrateCookieData($setup);
            $this->dropCookieColumns($setup);
        }

        $setup->endSetup();
    }

    /**
     * Create the extait_cookie_category_store table.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function createCategoryStoreTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = $connection->getTableName('extait_cookie_category_store');

        if ($connection->isTableExists($tableName) === true) {
            throw new \Zend_Db_Exception(__('The %1 table already exists.', $tableName));
        }

        $table = $connection->newTable($tableName)
            ->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Extait Cookie Category ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => 0],
                'Store View ID'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Cookie Category Name'
            )->addColumn(
                'description',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Cookie Category Description'
            )->addForeignKey(
                $setup->getFkName($tableName, 'category_id', 'extait_cookie_category', CategoryInterface::ID),
                'category_id',
                'extait_cookie_category',
                CategoryInterface::ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                'store_id',
                'store',
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('Cookie Category Data by Store View');

        $connection->createTable($table);
    }

    /**
     * Migrate Category Data from the extait_cookie_category table to the extait_cookie_category_store table.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function migrateCategoryData(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();

        $query = $connection->insertFromSelect(
            $connection->select()->from(
                ['ecc' => $connection->getTableName('extait_cookie_category')],
                ['category_id' => 'ecc.id', 'ecc.name', 'ecc.description']
            ),
            $connection->getTableName('extait_cookie_category_store'),
            ['category_id', 'name', 'description']
        );

        $connection->query($query);
    }

    /**
     * Drop unnecessary columns from the extait_cookie_category table.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function dropCategoryColumns(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = $connection->getTableName('extait_cookie_category');

        $connection->dropColumn($tableName, 'name');
        $connection->dropColumn($tableName, 'description');
    }

    /**
     * Create the extait_cookie_cookie_store table.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function createCookieStoreTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = $connection->getTableName('extait_cookie_cookie_store');

        if ($connection->isTableExists($tableName) === true) {
            throw new \Zend_Db_Exception(__('The %1 table already exists.', $tableName));
        }

        $table = $connection->newTable($tableName)
            ->addColumn(
                'cookie_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Extait Cookie ID'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => 0],
                'Store View ID'
            )->addColumn(
                'description',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Cookie Description'
            )->addForeignKey(
                $setup->getFkName($tableName, 'cookie_id', 'extait_cookie_cookie', CookieInterface::ID),
                'cookie_id',
                'extait_cookie_cookie',
                CookieInterface::ID,
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName($tableName, 'store_id', 'store', 'store_id'),
                'store_id',
                'store',
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('Cookie Data by Store View');

        $connection->createTable($table);
    }

    /**
     * Migrate Cookie Data from the extait_cookie_cookie table to the extait_cookie_cookie_store table.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function migrateCookieData(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();

        $query = $connection->insertFromSelect(
            $connection->select()->from(
                ['ecc' => $connection->getTableName('extait_cookie_cookie')],
                ['cookie_id' => 'ecc.id', 'ecc.description']
            ),
            $connection->getTableName('extait_cookie_cookie_store'),
            ['cookie_id', 'description']
        );

        $connection->query($query);
    }

    /**
     * Drop unnecessary columns from the extait_cookie_cookie table.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    private function dropCookieColumns(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = $connection->getTableName('extait_cookie_cookie');

        $connection->dropColumn($tableName, 'description');
    }
}
