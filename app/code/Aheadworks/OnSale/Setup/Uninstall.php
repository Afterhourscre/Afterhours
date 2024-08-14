<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Aheadworks\OnSale\Model\ResourceModel\Rule;
use Aheadworks\OnSale\Model\ResourceModel\Label;

/**
 * Class Uninstall
 *
 * @package Aheadworks\OnSale\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * {@inheritdoc}
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->uninstallTables($installer)
            ->uninstallConfigData($installer)
            ->uninstallFlagData($installer);

        $installer->endSetup();
    }

    /**
     * Uninstall all module tables
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallTables(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();
        $connection->dropTable($installer->getTable(RULE::PRODUCT_IDX_TABLE_NAME));
        $connection->dropTable($installer->getTable(RULE::PRODUCT_TABLE_NAME));
        $connection->dropTable($installer->getTable(RULE::WEBSITE_TABLE_NAME));
        $connection->dropTable($installer->getTable(RULE::FRONTEND_LABEL_TEXT_TABLE_NAME));
        $connection->dropTable($installer->getTable(RULE::MAIN_TABLE_NAME));
        $connection->dropTable($installer->getTable(LABEL::MAIN_TABLE_NAME));

        return $this;
    }

    /**
     * Uninstall module data from config
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallConfigData(SchemaSetupInterface $installer)
    {
        $configTable = $installer->getTable('core_config_data');
        $installer->getConnection()->delete($configTable, "`path` LIKE 'aw_onsale%'");

        return $this;
    }

    /**
     * Uninstall module data from flag table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallFlagData(SchemaSetupInterface $installer)
    {
        $flagTable = $installer->getTable('flag');
        $installer->getConnection()->delete($flagTable, "`flag_code` LIKE 'aw_onsale%'");

        return $this;
    }
}
