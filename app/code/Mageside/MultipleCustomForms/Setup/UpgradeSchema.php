<?php
/**
 * Copyright © 2017 Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.1.10', '<')) {
            $connection->addColumn(
                $installer->getTable('ms_custom_form'),
                'custom_class',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Css Class'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            if (!$installer->tableExists('ms_cf_recipient')) {
                $table = $connection
                    ->newTable($installer->getTable('ms_cf_recipient'))
                    ->addColumn(
                        'id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'Id'
                    )
                    ->addColumn(
                        'form_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false],
                        'Form Id'
                    )
                    ->addColumn(
                        'emails',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '64k',
                        ['nullable' => false],
                        'Emails'
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'ms_cf_recipient',
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

            if (!$installer->tableExists('ms_cf_recipient_dependency')) {
                $table = $connection
                    ->newTable($installer->getTable('ms_cf_recipient_dependency'))
                    ->addColumn(
                        'id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                        'Id'
                    )
                    ->addColumn(
                        'recipient_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false],
                        'Recipient Id'
                    )
                    ->addColumn(
                        'field_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false],
                        'Field Id'
                    )
                    ->addColumn(
                        'value',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '64k',
                        ['nullable' => false],
                        'Value'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            $installer->getTable('ms_cf_recipient_dependency'),
                            ['value'],
                            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                        ),
                        ['value'],
                        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'ms_cf_recipient_dependency',
                            'recipient_id',
                            'ms_cf_recipient',
                            'id'
                        ),
                        'recipient_id',
                        $installer->getTable('ms_cf_recipient'),
                        'id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'ms_cf_recipient_dependency',
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

            $select = $connection->select();
            $select->from($installer->getTable('ms_custom_form'), ['id', 'recipient_emails']);
            $forms = $connection->fetchAssoc($select);

            if (!empty($forms)) {
                $formsData = [];
                foreach ($forms as $form) {
                    $formsData[] = [
                        'form_id' => $form['id'],
                        'emails'  => $form['recipient_emails']
                    ];
                }

                $connection->insertArray(
                    $installer->getTable('ms_cf_recipient'),
                    ['form_id', 'emails'],
                    $formsData
                );
            }

            $connection->dropColumn($setup->getTable('ms_custom_form'), 'recipient_emails');
        }

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            /** Adding ms_custom_form_settings table */
            if (!$installer->tableExists('ms_custom_form_settings')) {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable('ms_custom_form_settings'))
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
                        'name',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [
                            'nullable' => true
                        ],
                        'Form Name'
                    )
                    ->addColumn(
                        'button_text',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [
                            'nullable' => true
                        ],
                        'Button Text'
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
                    ->addColumn(
                        'store_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'Store Id'
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'ms_custom_form_settings',
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

                $select = $connection->select();
                $select->from(
                    $installer->getTable('ms_custom_form'),
                    ['id', 'name', 'button_text', 'subject_email', 'redirect_url', 'success_message', 'fail_message', 'description']
                );
                $forms = $connection->fetchAssoc($select);

                if (!empty($forms)) {
                    $formsData = [];
                    foreach ($forms as $form) {
                        $formsData[] = [
                            'form_id'           => $form['id'],
                            'name'              => $form['name'],
                            'button_text'       => $form['button_text'],
                            'subject_email'     => $form['subject_email'],
                            'redirect_url'      => $form['redirect_url'],
                            'success_message'   => $form['success_message'],
                            'fail_message'      => $form['fail_message'],
                            'description'       => $form['description'],
                            'store_id'          => 0,
                        ];
                    }

                    $connection->insertArray(
                        $installer->getTable('ms_custom_form_settings'),
                        ['form_id', 'name', 'button_text', 'subject_email', 'redirect_url', 'success_message', 'fail_message', 'description', 'store_id'],
                        $formsData
                    );
                }

                $connection->dropColumn($setup->getTable('ms_custom_form'), 'name');
                $connection->dropColumn($setup->getTable('ms_custom_form'), 'button_text');
                $connection->dropColumn($setup->getTable('ms_custom_form'), 'subject_email');
                $connection->dropColumn($setup->getTable('ms_custom_form'), 'redirect_url');
                $connection->dropColumn($setup->getTable('ms_custom_form'), 'success_message');
                $connection->dropColumn($setup->getTable('ms_custom_form'), 'fail_message');
                $connection->dropColumn($setup->getTable('ms_custom_form'), 'description');
            }
            /** End adding ms_custom_form_settings table */

            /** Updating ms_cf_field_settings table */
            $connection->addColumn(
                $installer->getTable('ms_cf_field_settings'),
                'store_id',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    'comment'   => 'Store Id'
                ]
            );

            $select = $connection->select();
            $select->from(
                $installer->getTable('ms_cf_field'),
                ['id', 'title', 'placeholder', 'default_value', 'comment']
            );
            $fields = $connection->fetchAssoc($select);

            if (!empty($fields)) {
                $fieldsData = [];
                foreach ($fields as $field) {
                    foreach (['title', 'placeholder', 'default_value', 'comment'] as $key) {
                        $fieldsData[] = [
                            'field_id'  => $field['id'],
                            'key'       => $key,
                            'value'     => $field[$key],
                            'store_id'  => 0,
                        ];
                    }
                }

                $connection->insertArray(
                    $installer->getTable('ms_cf_field_settings'),
                    ['field_id', 'key', 'value', 'store_id'],
                    $fieldsData
                );
            }

            $connection->dropColumn($setup->getTable('ms_cf_field'), 'title');
            $connection->dropColumn($setup->getTable('ms_cf_field'), 'placeholder');
            $connection->dropColumn($setup->getTable('ms_cf_field'), 'default_value');
            $connection->dropColumn($setup->getTable('ms_cf_field'), 'comment');
            /** End updating ms_cf_field_settings table */

            /** Adding ms_cf_field_options_label table */
            if (!$installer->tableExists('ms_cf_field_options_label')) {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable('ms_cf_field_options_label'))
                    ->addColumn(
                        'option_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'Option Id'
                    )
                    ->addColumn(
                        'label',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [
                            'nullable' => false
                        ],
                        'Option Label'
                    )
                    ->addColumn(
                        'store_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'Store Id'
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'ms_cf_field_options_label',
                            'option_id',
                            'ms_cf_field_options',
                            'id'
                        ),
                        'option_id',
                        $installer->getTable('ms_cf_field_options'),
                        'id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    );
                $installer->getConnection()->createTable($table);

                $select = $connection->select();
                $select->from(
                    $installer->getTable('ms_cf_field_options'),
                    ['id', 'label']
                );
                $options = $connection->fetchAssoc($select);

                if (!empty($options)) {
                    $optionsData = [];
                    foreach ($options as $option) {
                        $optionsData[] = [
                            'option_id' => $option['id'],
                            'label'     => $option['label'],
                            'store_id'  => 0,
                        ];
                    }

                    $connection->insertArray(
                        $installer->getTable('ms_cf_field_options_label'),
                        ['option_id', 'label', 'store_id'],
                        $optionsData
                    );
                }

                $connection->dropColumn($setup->getTable('ms_cf_field_options'), 'label');
            }
            /** End adding ms_cf_field_options_label table */

            /** Updating ms_cf_recipient table */
            $connection->addColumn(
                $installer->getTable('ms_cf_recipient'),
                'store_id',
                [
                    'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    'comment'   => 'Store Id'
                ]
            );
            /** End updating ms_cf_recipient table */
        }

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            if (!$installer->tableExists('ms_cf_fieldset')) {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable('ms_cf_fieldset'))
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
                        'Fieldset Id'
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
                        'name',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [
                            'nullable' => true
                        ],
                        'Fieldset Name'
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
                    ->addForeignKey(
                        $installer->getFkName(
                            'ms_cf_fieldset',
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
            if (!$installer->tableExists('ms_cf_fieldset_settings')) {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable('ms_cf_fieldset_settings'))
                    ->addColumn(
                        'fieldset_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'Fieldset Id'
                    )
                    ->addColumn(
                        'title',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [
                            'nullable' => true
                        ],
                        'Fieldset Title'
                    )
                    ->addColumn(
                        'store_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                        ],
                        'Store Id'
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'ms_cf_fieldset_settings',
                            'fieldset_id',
                            'ms_cf_fieldset',
                            'id'
                        ),
                        'fieldset_id',
                        $installer->getTable('ms_cf_fieldset'),
                        'id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    );
                $installer->getConnection()->createTable($table);
            }
            if ($installer->tableExists('ms_cf_field')) {
                $connection->addColumn(
                    $installer->getTable('ms_cf_field'),
                    'fieldset_id',
                    [
                        'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'unsigned'  => true,
                        'nullable'  => true,
                        'default'   => '0',
                        'comment'   => 'Fieldset Id'
                    ]
                );
            }
        }
    }
}
