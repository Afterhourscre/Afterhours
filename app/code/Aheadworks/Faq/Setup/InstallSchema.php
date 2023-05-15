<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Faq\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 * @package Aheadworks\Faq\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'aw_faq_category'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_faq_category'))
            ->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Category ID'
            )->addColumn(
                'url_key',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Category URL-Key'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Category name'
            )->addColumn(
                'meta_title',
                Table::TYPE_TEXT,
                256,
                ['nullable' => true],
                'Category Meta title'
            )->addColumn(
                'meta_description',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Category Meta description'
            )->addColumn(
                'sort_order',
                Table::TYPE_SMALLINT,
                0,
                ['nullable' => true],
                'Order of displaying category on frontend'
            )->addColumn(
                'num_articles_to_display',
                Table::TYPE_SMALLINT,
                5,
                ['nullable' => true],
                'Number of articles to display on frontend'
            )->addColumn(
                'category_icon',
                Table::TYPE_TEXT,
                256,
                ['nullable' => true],
                'Category image icon'
            )->addColumn(
                'article_list_icon',
                Table::TYPE_TEXT,
                256,
                ['nullable' => true],
                'Article List Icon'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Category Creation Time'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Category Update Time'
            )->addColumn(
                'is_enable',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1'],
                'Is Category Enable'
            )->addIndex(
                $setup->getIdxName(
                    $installer->getTable('aw_faq_category'),
                    ['name'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )->setComment(
                'Aheadworks Faq Category'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_faq_article'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_faq_article'))
            ->addColumn(
                'article_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Article ID'
            )->addColumn(
                'url_key',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Article URL-Key'
            )->addColumn(
                'title',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Article title'
            )->addColumn(
                'meta_title',
                Table::TYPE_TEXT,
                256,
                ['nullable' => true],
                'Article Meta title'
            )->addColumn(
                'meta_description',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Article Meta description'
            )->addColumn(
                'content',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Article Content'
            )->addColumn(
                'sort_order',
                Table::TYPE_SMALLINT,
                0,
                ['nullable' => true],
                'Order of displaying article on frontend'
            )->addColumn(
                'votes_yes',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Number of positive votes'
            )->addColumn(
                'votes_no',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Number of negative votes'
            )->addColumn(
                'helpfulness_rating',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => true, 'default' => 0],
                'Percent of helpfulness rating'
            )->addColumn(
                'views_count',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Number of article views'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Article Creation Time'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Article Update Time'
            )->addColumn(
                'is_enable',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1'],
                'Is Article Enable'
            )->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Category ID'
            )->addForeignKey(
                $installer->getFkName('aw_faq_article', 'category_id', 'aw_faq_category', 'category_id'),
                'category_id',
                $installer->getTable('aw_faq_category'),
                'category_id',
                Table::ACTION_CASCADE
            )->addIndex(
                $setup->getIdxName(
                    $installer->getTable('aw_faq_article'),
                    ['title', 'content'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['title', 'content'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )->setComment(
                'Aheadworks Faq Article'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_faq_category_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_faq_category_store'))
            ->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Category ID'
            )->addColumn(
                'store_ids',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addIndex(
                $installer->getIdxName('aw_faq_category_store', ['store_ids']),
                ['store_ids']
            )->addForeignKey(
                $installer->getFkName('aw_faq_category_store', 'category_id', 'aw_faq_category', 'category_id'),
                'category_id',
                $installer->getTable('aw_faq_category'),
                'category_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('aw_faq_category_store', 'store_ids', 'store', 'store_id'),
                'store_ids',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment(
                'Aheadworks FAQ Category To Store Linkage Table'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_faq_article_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_faq_article_store'))
            ->addColumn(
                'article_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Article ID'
            )->addColumn(
                'store_ids',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addIndex(
                $installer->getIdxName('aw_faq_article_store', ['store_ids']),
                ['store_ids']
            )->addForeignKey(
                $installer->getFkName('aw_faq_article_store', 'article_id', 'aw_faq_article', 'article_id'),
                'article_id',
                $installer->getTable('aw_faq_article'),
                'article_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('aw_faq_article_store', 'store_ids', 'store', 'store_id'),
                'store_ids',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment(
                'Aheadworks FAQ Article To Store Linkage Table'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_faq_article_votes'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_faq_article_votes'))
            ->addColumn(
                'votes_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Votes ID'
            )->addColumn(
                'article_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Article ID'
            )->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Customer Id'
            )->addColumn(
                'visitor_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Visitor Id'
            )->addColumn(
                'action',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Vote action'
            )->addIndex(
                $installer->getIdxName('aw_faq_article_votes', ['article_id']),
                ['article_id']
            )->addForeignKey(
                $installer->getFkName('aw_faq_article_votes', 'article_id', 'aw_faq_article', 'article_id'),
                'article_id',
                $installer->getTable('aw_faq_article'),
                'article_id',
                Table::ACTION_CASCADE
            )->setComment(
                'Aheadworks FAQ Votes Table'
            );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
