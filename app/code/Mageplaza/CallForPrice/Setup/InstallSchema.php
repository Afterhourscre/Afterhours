<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Mageplaza\CallForPrice\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        /** Table mageplaza_callforprice_rules */
        if ($installer->tableExists('mageplaza_callforprice_rules')) {
            $connection->dropTable($installer->getTable('mageplaza_callforprice_rules'));
        }
        $table = $connection
            ->newTable($installer->getTable('mageplaza_callforprice_rules'))
            ->addColumn('rule_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'primary'  => true,
                'unsigned' => true
            ], 'Rule ID')
            ->addColumn('name', Table::TYPE_TEXT, 255, [], 'Name')
            ->addColumn('rule_content', Table::TYPE_TEXT, '2M', [], 'Rule Content')
            ->addColumn('store_ids', Table::TYPE_TEXT, null, [], 'Store Id')
            ->addColumn('customer_group_ids', Table::TYPE_TEXT, null, [], 'Customer Group')
            ->addColumn('action', Table::TYPE_TEXT, 255, [], 'Action')
            ->addColumn('url_redirect', Table::TYPE_TEXT, '64k', [], 'Url Redirect')
            ->addColumn('quote_heading', Table::TYPE_TEXT, 255, [], 'Quote Heading')
            ->addColumn('quote_description', Table::TYPE_TEXT, '64k', [], 'Quote Description')
            ->addColumn('status', Table::TYPE_SMALLINT, 1, [], 'Status')
            ->addColumn('show_fields', Table::TYPE_TEXT, null, [], 'Show Fields')
            ->addColumn('required_fields', Table::TYPE_TEXT, null, [], 'Required Fields')
            ->addColumn('conditions_serialized', Table::TYPE_TEXT, '2M', [], 'Conditions Serialized')
            ->addColumn('attribute_code', Table::TYPE_TEXT, 255, [], 'Attribute Code')
            ->addColumn('button_label', Table::TYPE_TEXT, 255, [], 'Button Label')
            ->addColumn('priority', Table::TYPE_SMALLINT, null, [], 'Priority')
            ->addColumn('to_date', Table::TYPE_TIMESTAMP, null, [], 'Rule To Date')
            ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Rule Created At')
            ->addColumn('rule_description', Table::TYPE_TEXT, '64k', [], 'Rule Description')
            ->addColumn('enable_terms', Table::TYPE_SMALLINT, 1, [], 'Enable Terms and Condition')
            ->addColumn('url_terms', Table::TYPE_TEXT, '64k', [], 'Url Terms and Conditions')
            ->setComment('Rules Table');
        $connection->createTable($table);

        /** Table mageplaza_callforprice_requests */
        if ($installer->tableExists('mageplaza_callforprice_requests')) {
            $connection->dropTable($installer->getTable('mageplaza_callforprice_requests'));
        }
        $table = $connection
            ->newTable($installer->getTable('mageplaza_callforprice_requests'))
            ->addColumn('request_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'nullable' => false,
                'primary'  => true,
                'unsigned' => true
            ], 'Request ID')
            ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, [], 'Created Date')
            ->addColumn('sku', Table::TYPE_TEXT, 255, ['nullable => false'], 'Status')
            ->addColumn('product_id', Table::TYPE_INTEGER, null, [], 'Product Id')
            ->addColumn('item_product', Table::TYPE_TEXT, '2M', ['nullable => false'], 'Item Product')
            ->addColumn('store_ids', Table::TYPE_TEXT, null, [], 'Store Id')
            ->addColumn('customer_group_ids', Table::TYPE_TEXT, null, [], 'Customer Group')
            ->addColumn('status', Table::TYPE_TEXT, 255, ['nullable => false'], 'Status')
            ->addColumn('name', Table::TYPE_TEXT, '64k', ['nullable => false'], 'Name')
            ->addColumn('email', Table::TYPE_TEXT, 255, ['nullable => false'], 'email')
            ->addColumn('phone', Table::TYPE_TEXT, 255, ['nullable => false'], 'Phone')
            ->addColumn('customer_note', Table::TYPE_TEXT, '64k', [], 'Customer Note')
            ->addColumn('internal_note', Table::TYPE_TEXT, '64k', [], 'Internal Note')
            ->addColumn('rank_request', Table::TYPE_INTEGER, null, [], 'Rank Request')
            ->setComment('Requests Table');
        $connection->createTable($table);

        $installer->endSetup();
    }
}
