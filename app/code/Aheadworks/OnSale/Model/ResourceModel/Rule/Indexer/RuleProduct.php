<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer;

use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\RuleRepositoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Indexer\Table\StrategyInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Indexer\Model\ResourceModel\AbstractResource;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct\DataCollector as DataCollector;
use Aheadworks\OnSale\Model\Indexer\Rule\ProductLoader;
use Aheadworks\OnSale\Model\ResourceModel\Rule as RuleResourceModel;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer\RuleProduct\RuleProductInterface;

/**
 * Class RuleProduct
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\Indexer
 */
class RuleProduct extends AbstractResource implements IdentityInterface
{
    /**
     * @var int
     */
    const INSERT_PER_QUERY = 500;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var DataCollector;
     */
    private $dataCollector;

    /**
     * @var array
     */
    private $entities = [];

    /**
     * @var ProductLoader
     */
    private $productLoader;

    /**
     * @param Context $context
     * @param StrategyInterface $tableStrategy
     * @param RuleRepositoryInterface $ruleRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param EventManagerInterface $eventManager
     * @param ProductLoader $productLoader
     * @param DataCollector $dataCollector
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        StrategyInterface $tableStrategy,
        RuleRepositoryInterface $ruleRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        EventManagerInterface $eventManager,
        ProductLoader $productLoader,
        DataCollector $dataCollector,
        $connectionName = null
    ) {
        parent::__construct($context, $tableStrategy, $connectionName);
        $this->ruleRepository = $ruleRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataCollector = $dataCollector;
        $this->eventManager = $eventManager;
        $this->productLoader = $productLoader;
    }

    /**
     * Define main product post index table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(RuleResourceModel::PRODUCT_TABLE_NAME, RuleProductInterface::RULE_PRODUCT_ID);
    }

    /**
     * Reindex all rule product data
     *
     * @return $this
     * @throws \Exception
     */
    public function reindexAll()
    {
        $this->tableStrategy->setUseIdxTable(true);
        $this->clearTemporaryIndexTable();
        $this->beginTransaction();
        try {
            $toInsert = $this->prepareDataToInsert();
            $this->prepareInsertToTable($toInsert);
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        $this->syncData();
        $this->dispatchCleanCacheByTags($toInsert);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function syncData()
    {
        try {
            $this->getConnection()->truncateTable($this->getMainTable());
            $this->insertFromTable($this->getIdxTable(), $this->getMainTable(), false);
        } catch (\Exception $e) {
            throw $e;
        }
        return $this;
    }

    /**
     * Reindex product rule data for defined ids
     *
     * @param array|int $ids
     * @return $this
     * @throws \Exception
     * @throws LocalizedException
     */
    public function reindexRows($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $toUpdate = $this->prepareDataToUpdate($ids);
        $this->beginTransaction();
        try {
            $this->getConnection()->delete(
                $this->getMainTable(),
                ['product_id IN (?)' => $ids]
            );
            $this->prepareInsertToTable($toUpdate, false);
            $this->commit();
            $this->dispatchCleanCacheByTags($toUpdate);
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Dispatch clean_cache_by_tags event
     *
     * @param array $entities
     * @return void
     */
    private function dispatchCleanCacheByTags($entities = [])
    {
        $this->entities = $entities;
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
    }

    /**
     * {@inheritdoc}
     */
    public function clearTemporaryIndexTable()
    {
        $this->getConnection()->truncateTable($this->getIdxTable());
    }

    /**
     * Prepare and return data for insert to index table
     *
     * @return array
     * @throws LocalizedException
     */
    private function prepareDataToInsert()
    {
        $rules = $this->getActiveRules();

        $result = [];
        foreach ($rules as $rule) {
            $data = $this->dataCollector->prepareRuleData($rule);
            $result = array_merge($result, $data);
        }
        return $result;
    }

    /**
     * Prepare and return data for update to index table
     *
     * @param array $ids
     * @return array
     * @throws LocalizedException
     */
    private function prepareDataToUpdate($ids)
    {
        $rules = $this->getActiveRules();
        $products = $this->productLoader->getProducts($ids);

        $result = [];
        foreach ($rules as $rule) {
            foreach ($products as $product) {
                $data = $this->dataCollector->prepareRuleProductData($rule, $product);
                $result = array_merge($result, $data);
            }
        }
        return $result;
    }

    /**
     * @return RuleInterface[]
     * @throws LocalizedException
     */
    private function getActiveRules()
    {
        $this->searchCriteriaBuilder->addFilter(RuleInterface::IS_ACTIVE, ['eq' => true]);
        return $this->ruleRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();
    }

    /**
     * Prepare data and partial insert to index or main table
     *
     * @param $data
     * @param bool $intoIndexTable
     * @return $this
     * @throws LocalizedException
     */
    private function prepareInsertToTable($data, $intoIndexTable = true)
    {
        $counter = 0;
        $toInsert = [];
        foreach ($data as $row) {
            $counter++;
            $toInsert[] = $row;
            if ($counter % self::INSERT_PER_QUERY == 0) {
                $this->insertToTable($toInsert, $intoIndexTable);
                $toInsert = [];
            }
        }
        $this->insertToTable($toInsert, $intoIndexTable);
        return $this;
    }

    /**
     * Insert to index table
     *
     * @param $toInsert
     * @param bool $intoIndexTable
     * @return $this
     * @throws LocalizedException
     */
    private function insertToTable($toInsert, $intoIndexTable = true)
    {
        $table = $intoIndexTable
            ? $this->getTable($this->getIdxTable())
            : $this->getMainTable();
        if (count($toInsert)) {
            $this->getConnection()->insertMultiple(
                $table,
                $toInsert
            );
        }
        return $this;
    }

    /**
     * Get affected cache tags
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->entities as $entity) {
            $identities[] = ProductModel::CACHE_TAG . '_' . $entity[RuleProductInterface::PRODUCT_ID];
        }
        return array_unique($identities);
    }
}
