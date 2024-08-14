<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MagentoChatSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MagentoChatSystem\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /*
         * Create table 'chatsystem_agent_rating'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('chatsystem_agent_rating'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'customer ID'
            )
            ->addColumn(
                'agent_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'agent ID'
            )
            ->addColumn(
                'agent_unique_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Agent Unique Id'
            )
            ->addColumn(
                'rating',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Ratings'
            )
            ->addColumn(
                'rating_comment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Ratings comment'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Status'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->setComment('Agent Rating Table');
        $setup->getConnection()->createTable($table);

        $tableRreport = $setup->getConnection()->newTable($setup->getTable('chatsystem_report'));

        $tableRreport->addColumn(
            'report_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $tableRreport->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'customer_id'
        );

        $tableRreport->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'customer_name'
        );

        $tableRreport->addColumn(
            'agent_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'agent_id'
        );

        $tableRreport->addColumn(
            'subject',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'subject'
        );

        $tableRreport->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'content'
        );

        $setup->getConnection()->createTable($tableRreport);
        
        
        $setup->getConnection()->addColumn(
            $setup->getTable('chatsystem_agentdata'),
            'agent_type',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => '11',
            'nullable' => false,
            'default' => '0',
            'comment' => 'Agent Type']
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('chatsystem_assigned_chat'),
            'assigned_at',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                'nullable' => true,
                'comment' => 'Chat assigned at'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('chatsystem_history'),
            'sender_name',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => '255',
            'nullable' => false,
            'default' => '',
            'comment' => 'Message Sender']
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('chatsystem_history'),
            'receiver_name',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => '255',
            'nullable' => false,
            'default' => '',
            'comment' => 'Message Receiver']
        );

        /**
         * drop foreign keys for agent ID
         */
        $setup->getConnection()->dropForeignKey(
            $setup->getTable('chatsystem_agentdata'),
            $setup->getFkName(
                'chatsystem_agentdata',
                'agent_id',
                'admin_user',
                'user_id'
            )
        );

        $setup->endSetup();
    }
}
