<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\Relation\Website;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\OnSale\Model\ResourceModel\Rule as RuleResourceModel;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\Relation\Website
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * Read column
     */
    const WEBSITE_ID = 'website_id';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var string
     */
    private $ruleWebsiteTableName;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->ruleWebsiteTableName = $this->resourceConnection->getTableName(RuleResourceModel::WEBSITE_TABLE_NAME);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFoOnSalelParameter)
     */
    public function execute($entity, $arguments = [])
    {
        /** @var RuleInterface $entity */
        $entityId = (int)$entity->getRuleId();
        if (!$entityId) {
            return $entity;
        }

        $websiteIds = $this->getWebsiteData($entityId);
        $entity->setWebsiteIds($websiteIds);

        return $entity;
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(RuleInterface::class)->getEntityConnectionName()
        );
    }

    /**
     * Retrieve website ids
     *
     * @param int $entityId
     * @return array
     * @throws \Exception
     */
    public function getWebsiteData($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->ruleWebsiteTableName, self::WEBSITE_ID)
            ->where('rule_id = :id');
        return $connection->fetchCol($select, ['id' => $entityId]);
    }
}
