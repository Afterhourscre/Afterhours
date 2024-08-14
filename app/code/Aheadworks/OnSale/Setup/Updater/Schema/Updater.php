<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Setup\Updater\Schema;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\OnSale\Model\ResourceModel\Rule;
use Aheadworks\OnSale\Model\ResourceModel\Label;

/**
 * Class Updater
 *
 * @package Aheadworks\OnSale\Setup\Updater\Schema
 */
class Updater
{
    /**
     * Update for 1.1.0 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function update110(SchemaSetupInterface $setup)
    {
        $this->addAdditionalLabelTextFieldsToRuleFrontendLabelTable($setup);
        $this->addAdditionalLabelTextFieldsToRuleProductTables($setup);
        $this->addAdditionalCustomizeCssFieldsToLabelTable($setup);

        return $this;
    }

    /**
     * Add additional text fields to rule frontend label table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addAdditionalLabelTextFieldsToRuleFrontendLabelTable(SchemaSetupInterface $installer)
    {
        $tableName = Rule::FRONTEND_LABEL_TEXT_TABLE_NAME;
        if ($installer->getConnection()->tableColumnExists($installer->getTable($tableName), 'value')) {
            $installer->getConnection()->changeColumn(
                $installer->getTable($tableName),
                'value',
                'value_large',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'length' => 255,
                    'comment' => 'Large Label Text',
                ]
            );
        }
        $this->addColumnsToTable(
            $installer,
            [
                [
                    'fieldName' => 'value_medium',
                    'config' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => false,
                        'length' => 255,
                        'comment' => 'Medium Label Text'
                    ]
                ],
                [
                    'fieldName' => 'value_small',
                    'config' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => false,
                        'length' => 255,
                        'comment' => 'Small Label Text'
                    ]
                ]
            ],
            $tableName
        );

        return $this;
    }

    /**
     * Add additional text fields to rule product table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addAdditionalLabelTextFieldsToRuleProductTables(SchemaSetupInterface $installer)
    {
        $tables = [
            Rule::PRODUCT_TABLE_NAME,
            Rule::PRODUCT_IDX_TABLE_NAME
        ];

        foreach ($tables as $table) {
            if ($installer->getConnection()->tableColumnExists($installer->getTable($table), 'label_text')) {
                $installer->getConnection()->changeColumn(
                    $installer->getTable($table),
                    'label_text',
                    'label_text_large',
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => 255,
                        'comment' => 'Large Label Text',
                    ]
                );
            }
            $this->addColumnsToTable(
                $installer,
                [
                    [
                        'fieldName' => 'label_text_medium',
                        'config' => [
                            'type' => Table::TYPE_TEXT,
                            'nullable' => true,
                            'length' => 255,
                            'comment' => 'Medium Label Text'
                        ]
                    ],
                    [
                        'fieldName' => 'label_text_small',
                        'config' => [
                            'type' => Table::TYPE_TEXT,
                            'nullable' => true,
                            'length' => 255,
                            'comment' => 'Small Label Text'
                        ]
                    ]
                ],
                $table
            );
        }

        return $this;
    }

    /**
     * Add additional customize css fields to label table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addAdditionalCustomizeCssFieldsToLabelTable(SchemaSetupInterface $installer)
    {
        $tableName = Label::MAIN_TABLE_NAME;
        if ($installer->getConnection()->tableColumnExists(
            $installer->getTable($tableName),
            'customize_css_container'
        )) {
            $installer->getConnection()->changeColumn(
                $installer->getTable($tableName),
                'customize_css_container',
                'customize_css_container_large',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'length' => '64k',
                    'comment' => 'Customize CSS Container for large label',
                ]
            );
        }
        if ($installer->getConnection()->tableColumnExists(
            $installer->getTable($tableName),
            'customize_css_label'
        )) {
            $installer->getConnection()->changeColumn(
                $installer->getTable($tableName),
                'customize_css_label',
                'customize_css_label_large',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'length' => '64k',
                    'comment' => 'Customize CSS Label for large label',
                ]
            );
        }
        $this->addColumnsToTable(
            $installer,
            [
                [
                    'fieldName' => 'customize_css_container_medium',
                    'config' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => false,
                        'length' => '64k',
                        'comment' => 'Customize CSS Container for medium label'
                    ]
                ],
                [
                    'fieldName' => 'customize_css_label_medium',
                    'config' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => false,
                        'length' => '64k',
                        'comment' => 'Customize CSS Label for medium label',
                    ]
                ],
                [
                    'fieldName' => 'customize_css_container_small',
                    'config' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => false,
                        'length' => '64k',
                        'comment' => 'Customize CSS Container for small label'
                    ]
                ],
                [
                    'fieldName' => 'customize_css_label_small',
                    'config' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => false,
                        'length' => '64k',
                        'comment' => 'Customize CSS Label for small label',
                    ]
                ]
            ],
            $tableName
        );

        return $this;
    }

    /**
     * Add columns to table
     *
     * @param SchemaSetupInterface $setup
     * @param array $columnsConfig
     * @param string $tableName
     * @return $this
     */
    private function addColumnsToTable($setup, $columnsConfig, $tableName)
    {
        $connection = $setup->getConnection();
        $tableName = $setup->getTable($tableName);
        foreach ($columnsConfig as $fieldConfig) {
            $fieldName = $fieldConfig['fieldName'];
            if ($connection->tableColumnExists($tableName, $fieldName)) {
                continue;
            }
            $connection->addColumn(
                $tableName,
                $fieldName,
                $fieldConfig['config']
            );
        }

        return $this;
    }
}
