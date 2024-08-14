<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Relation\Website;

use Aheadworks\RewardPoints\Api\Data\EarnRuleInterface;
use Aheadworks\RewardPoints\Model\ResourceModel\EarnRule as EarnRuleResource;
use Aheadworks\RewardPoints\Model\EarnRule;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class ReadHandler
 * @package Aheadworks\RewardPoints\Model\ResourceModel\EarnRule\Relation\Website
 * @codeCoverageIgnore
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        /** @var EarnRule $entity */
        if ($entityId = (int)$entity->getId()) {
            $websiteData = $this->getWebsiteData($entityId);
            $this->addWebsiteDataToEntity($entity, $websiteData);
        }
        return $entity;
    }

    /**
     * Retrieve website data from corresponding table
     *
     * @param int $entityId
     * @return array
     */
    private function getWebsiteData($entityId)
    {
        $websiteData = [];
        try {
            $connection = $this->getConnection();
            $tableName = $this->getTableName();
            $select = $connection->select()
                ->from($tableName, 'website_id')
                ->where('rule_id = :id');
            $websiteData = $connection->fetchCol($select, ['id' => $entityId]);
        } catch (\Exception $exception) {
        }
        return $websiteData;
    }

    /**
     * Add extracted website data to the corresponding entity
     *
     * @param EarnRule $entity
     * @param array $websiteData
     * @return EarnRule
     */
    private function addWebsiteDataToEntity($entity, $websiteData)
    {
        if (!empty($websiteData)) {
            $entity->setWebsiteIds($websiteData);
        }
        return $entity;
    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(EarnRuleInterface::class)->getEntityConnectionName()
        );
    }

    /**
     * Get table name
     *
     * @return string
     */
    private function getTableName()
    {
        return $this->resourceConnection->getTableName(EarnRuleResource::WEBSITE_TABLE_NAME);
    }
}
