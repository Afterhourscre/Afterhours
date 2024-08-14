<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\Relation\LabelText;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterfaceFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\OnSale\Model\ResourceModel\Rule as RuleResourceModel;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\Relation\LabelText
 */
class SaveHandler implements ExtensionInterface
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
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        /** @var RuleInterface $entity */
        $entityId = (int)$entity->getRuleId();
        $connection = $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(RuleInterface::class)->getEntityConnectionName()
        );
        $tableName = $this->resourceConnection->getTableName(RuleResourceModel::FRONTEND_LABEL_TEXT_TABLE_NAME);
        $connection->delete($tableName, ['rule_id = ?' => $entityId]);

        $labelTextStoreValuesToInsert = [];
        /** @var LabelTextStoreValueInterface $labelTextStoreValueEntity */
        foreach ($entity->getFrontendLabelTextStoreValues() as $labelTextStoreValueEntity) {
            if (!$labelTextStoreValueEntity->getValueLarge()) {
                continue;
            }
            $labelTextStoreValuesToInsert[] = [
                'rule_id' => $entityId,
                'store_id' => $labelTextStoreValueEntity->getStoreId(),
                'value_large' => $labelTextStoreValueEntity->getValueLarge(),
                'value_medium' => $labelTextStoreValueEntity->getValueMedium(),
                'value_small' => $labelTextStoreValueEntity->getValueSmall()
            ];
        }
        if ($labelTextStoreValuesToInsert) {
            $connection->insertMultiple($tableName, $labelTextStoreValuesToInsert);
        }

        return $entity;
    }
}
