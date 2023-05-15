<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\Relation\LabelText;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterfaceFactory;
use Aheadworks\OnSale\Model\Rule\LabelText\StoreResolver as LabelTextStoreResolver;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\OnSale\Model\ResourceModel\Rule as RuleResourceModel;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\Relation\FrontendLabelText
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
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var LabelTextStoreValueInterfaceFactory
     */
    private $labelTextStoreValueFactory;

    /**
     * @var LabelTextStoreResolver
     */
    private $labelTextStoreResolver;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param LabelTextStoreValueInterfaceFactory $labelTextStoreValueFactory
     * @param LabelTextStoreResolver $labelTextStoreResolver
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        LabelTextStoreValueInterfaceFactory $labelTextStoreValueFactory,
        LabelTextStoreResolver $labelTextStoreResolver
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->labelTextStoreValueFactory = $labelTextStoreValueFactory;
        $this->labelTextStoreResolver = $labelTextStoreResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        /** @var RuleInterface $entity */
        if ($entityId = (int)$entity->getRuleId()) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(RuleInterface::class)->getEntityConnectionName()
            );
            $select = $connection->select()
                ->from($this->resourceConnection->getTableName(RuleResourceModel::FRONTEND_LABEL_TEXT_TABLE_NAME))
                ->where('rule_id = :id');
            $labelTextStoreValuesData = $connection->fetchAll($select, ['id' => $entityId]);

            $labelTextStoreValues = [];
            foreach ($labelTextStoreValuesData as $labelTextStoreValue) {
                /** @var LabelTextStoreValueInterface $labelTextStoreValueEntity */
                $labelTextStoreValueEntity = $this->labelTextStoreValueFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $labelTextStoreValueEntity,
                    $labelTextStoreValue,
                    LabelTextStoreValueInterface::class
                );
                $labelTextStoreValues[] = $labelTextStoreValueEntity;
            }
            $entity
                ->setFrontendLabelTextStoreValues($labelTextStoreValues)
                ->setFrontendLabelText(
                    $this->labelTextStoreResolver->getLabelTextAsObject($labelTextStoreValues, $arguments['store_id'])
                );
        }
        return $entity;
    }
}
