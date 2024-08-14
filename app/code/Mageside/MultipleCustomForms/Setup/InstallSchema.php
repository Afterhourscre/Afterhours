<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table ms_custom_form
         */
        if (!$installer->tableExists('ms_custom_form')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('ms_custom_form'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Form Id'
                )
                ->addColumn(
                    'name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Form Name'
                )
                ->addColumn(
                    'code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    32,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Form Code'
                )
                ->addColumn(
                    'recaptcha',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    24,
                    [
                        'nullable' => false,
                        'default' => 'disabled'
                    ],
                    'reCaptcha'
                )
                ->addColumn(
                    'button_text',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Button Text'
                )
                ->addColumn(
                    'submission_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    30,
                    [
                        'nullable' => false
                    ],
                    'Submission Type'
                )
                ->addColumn(
                    'recipient_emails',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => true
                    ],
                    'Recipient Emails'
                )
                ->addColumn(
                    'subject_email',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1024,
                    [
                        'nullable' => true
                    ],
                    'Subject Email'
                )
                ->addColumn(
                    'redirect_url',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1024,
                    [
                        'nullable' => true
                    ],
                    'Redirect URL'
                )
                ->addColumn(
                    'form_status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    1,
                    [
                        'nullable' => false,
                        'default' => '1'
                    ],
                    'Form Status'
                )
                ->addColumn(
                    'after_submit',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    24,
                    [
                        'nullable' => false,
                    ],
                    'After Submit Action'
                )
                ->addColumn(
                    'success_message',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => true,
                    ],
                    'Success Message'
                )
                ->addColumn(
                    'fail_message',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => true,
                    ],
                    'Fail Message'
                )
                ->addColumn(
                    'description',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => true,
                    ],
                    'Description'
                )
                ->addIndex(
                    $installer->getIdxName(
                        'ms_custom_form',
                        ['code'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['code'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                );
            $installer->getConnection()->createTable($table);
        }

        /**
         * Create table ms_cf_field
         */
        if (!$installer->tableExists('ms_cf_field')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('ms_cf_field'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Field Id'
                )
                ->addColumn(
                    'form_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Form Id'
                )
                ->addColumn(
                    'title',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Field Title'
                )
                ->addColumn(
                    'type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    64,
                    [
                        'nullable' => false
                    ],
                    'Field Type'
                )
                ->addColumn(
                    'placeholder',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    64,
                    [
                        'nullable' => true
                    ],
                    'Placeholder in Field'
                )
                ->addColumn(
                    'validation',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => true
                    ],
                    'Validation'
                )
                ->addColumn(
                    'default_value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true
                    ],
                    'Default Value for Field'
                )
                ->addColumn(
                    'required',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ],
                    'Required'
                )
                ->addColumn(
                    'position',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ],
                    'Position'
                )
                ->addColumn(
                    'show_in_grid',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    1,
                    [
                        'nullable' => false,
                        'default' => '1'
                    ],
                    'Show in Grid'
                )
                ->addColumn(
                    'comment',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1000,
                    [
                        'nullable' => true,
                    ],
                    'Comment'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'ms_cf_field',
                        'form_id',
                        'ms_custom_form',
                        'id'
                    ),
                    'form_id',
                    $installer->getTable('ms_custom_form'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        /**
         * Create table ms_cf_field_options
         */
        if (!$installer->tableExists('ms_cf_field_options')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('ms_cf_field_options'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Options Id'
                )
                ->addColumn(
                    'field_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Field Id'
                )
                ->addColumn(
                    'label',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Field Title'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'ms_cf_field_options',
                        'field_id',
                        'ms_cf_field',
                        'id'
                    ),
                    'field_id',
                    $installer->getTable('ms_cf_field'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        /**
         * Create table ms_cf_submission
         */
        if (!$installer->tableExists('ms_cf_submission')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('ms_cf_submission'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Id'
                )
                ->addColumn(
                    'form_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Form Id'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                    ],
                    'Created At'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'ms_cf_submission',
                        'form_id',
                        'ms_custom_form',
                        'id'
                    ),
                    'form_id',
                    $installer->getTable('ms_custom_form'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        /**
         * Create table ms_cf_submission_varchar
         */
        if (!$installer->tableExists('ms_cf_submission_varchar')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('ms_cf_submission_varchar'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Field Id'
                )
                ->addColumn(
                    'submission_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Submission Id'
                )
                ->addColumn(
                    'form_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Form Id'
                )
                ->addColumn(
                    'field_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Field Id'
                )
                ->addColumn(
                    'value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Value'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'ms_cf_submission_varchar',
                        'submission_id',
                        'ms_cf_submission',
                        'id'
                    ),
                    'submission_id',
                    $installer->getTable('ms_cf_submission'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'ms_cf_submission_varchar',
                        'field_id',
                        'ms_cf_field',
                        'id'
                    ),
                    'field_id',
                    $installer->getTable('ms_cf_field'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        /**
         * Create table ms_cf_submission_integer
         */
        if (!$installer->tableExists('ms_cf_submission_integer')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('ms_cf_submission_integer'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Id'
                )
                ->addColumn(
                    'submission_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Submission Id'
                )
                ->addColumn(
                    'form_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Form Id'
                )
                ->addColumn(
                    'field_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Field Id'
                )
                ->addColumn(
                    'value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Value'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'ms_cf_submission_integer',
                        'submission_id',
                        'ms_cf_submission',
                        'id'
                    ),
                    'submission_id',
                    $installer->getTable('ms_cf_submission'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addForeignKey(
                    $installer->getFkName(
                        'ms_cf_submission_integer',
                        'field_id',
                        'ms_cf_field',
                        'id'
                    ),
                    'field_id',
                    $installer->getTable('ms_cf_field'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        /**
         * Create table ms_cf_submission_text
         */
        if (!$installer->tableExists('ms_cf_submission_text')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('ms_cf_submission_text'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Submission Integer Id'
                )
                ->addColumn(
                    'submission_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Submission Id'
                )
                ->addColumn(
                    'form_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Form Id'
                )
                ->addColumn(
                    'field_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Field Id'
                )
                ->addColumn(
                    'value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Value'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'ms_cf_submission_text',
                        'submission_id',
                        'ms_cf_submission',
                        'id'
                    ),
                    'submission_id',
                    $installer->getTable('ms_cf_submission'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addForeignKey(
                    $installer->getFkName(
                        'ms_cf_submission_text',
                        'field_id',
                        'ms_cf_field',
                        'id'
                    ),
                    'field_id',
                    $installer->getTable('ms_cf_field'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        /**
         * Create table ms_cf_field_settings
         */
        if (!$installer->tableExists('ms_cf_field_settings')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('ms_cf_field_settings'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Id'
                )
                ->addColumn(
                    'field_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Field Id'
                )
                ->addColumn(
                    'key',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false,
                    ],
                    'Key'
                )

                ->addColumn(
                    'value',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => false,
                    ],
                    'Value'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'ms_cf_field_settings',
                        'field_id',
                        'ms_cf_field',
                        'id'
                    ),
                    'field_id',
                    $installer->getTable('ms_cf_field'),
                    'id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
