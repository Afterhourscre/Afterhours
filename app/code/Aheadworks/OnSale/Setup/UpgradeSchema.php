<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Setup;

use Aheadworks\OnSale\Model\Indexer\Rule\Processor;
use Aheadworks\OnSale\Model\ResourceModel\Rule;
use Magento\Framework\DB\Ddl\Trigger;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Indexer\IndexerRegistry;
use Aheadworks\OnSale\Setup\Updater\Schema\Updater as SchemaUpdater;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\OnSale\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var SchemaUpdater
     */
    private $updater;

    /**
     * @param ResourceConnection $resource
     * @param IndexerRegistry $indexerRegistry
     * @param SchemaUpdater $updater
     */
    public function __construct(
        ResourceConnection $resource,
        IndexerRegistry $indexerRegistry,
        SchemaUpdater $updater
    ) {
        $this->resource = $resource;
        $this->indexerRegistry = $indexerRegistry;
        $this->updater = $updater;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->updateMview($setup);
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->updater->update110($setup);
        }

        $setup->endSetup();
    }

    /**
     * Update mview is needed
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Exception
     */
    private function updateMview($setup)
    {
        $indexer = $this->indexerRegistry->get(Processor::INDEXER_ID);
        $connection = $setup->getConnection();

        if ($indexer->isScheduled()) {
            foreach (Trigger::getListOfEvents() as $event) {
                $triggerName = $this->getTriggerName($event);
                $connection->dropTrigger($triggerName);
            }
            $indexer->getView()->unsubscribe()->subscribe();
        }

        return $this;
    }

    /**
     * Retrieve trigger name
     *
     * @param string $event
     * @return string
     */
    private function getTriggerName($event)
    {
        return strtolower(
            $this->resource->getTriggerName(
                $this->resource->getTableName(Rule::MAIN_TABLE_NAME),
                Trigger::TIME_AFTER,
                $event
            )
        );
    }
}
