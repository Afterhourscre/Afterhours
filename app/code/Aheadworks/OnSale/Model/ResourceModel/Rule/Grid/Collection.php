<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule\Grid;

use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Model\ResourceModel\Label as LabelResourceModel;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Aheadworks\OnSale\Model\ResourceModel\Rule\Collection as RuleCollection;
use Aheadworks\OnSale\Model\Rule\LabelText\StoreResolver as LabelTextStoreResolver;
use Aheadworks\OnSale\Model\Rule\ObjectDataProcessor as RuleDataProcessor;

/**
 * Class Collection
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule\Grid
 */
class Collection extends RuleCollection implements SearchResultInterface
{
    /**#@+
     * Constants for label data in grid
     */
    const LABEL_POSITION = 'label_position';
    const LABEL_NAME = 'label_name';
    /**#@-*/

    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param LabelTextStoreResolver $labelTextStoreResolver
     * @param RuleDataProcessor $ruleDataProcessor
     * @param mixed|null $mainTable
     * @param AbstractDb $eventPrefix
     * @param mixed $eventObject
     * @param mixed $resourceModel
     * @param string $model
     * @param AdapterInterface|null $connection
     * @param AbstractDb $resource
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        LabelTextStoreResolver $labelTextStoreResolver,
        RuleDataProcessor $ruleDataProcessor,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = Document::class,
        $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $labelTextStoreResolver,
            $ruleDataProcessor,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, [self::LABEL_NAME])) {
            $this->addFilter($field, $condition, 'public');
            return $this;
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            LabelResourceModel::MAIN_TABLE_NAME,
            RuleInterface::LABEL_ID,
            LabelInterface::LABEL_ID,
            LabelInterface::NAME,
            self::LABEL_NAME
        );
        $this->attachRelationTable(
            LabelResourceModel::MAIN_TABLE_NAME,
            RuleInterface::LABEL_ID,
            LabelInterface::LABEL_ID,
            LabelInterface::POSITION,
            self::LABEL_POSITION
        );

        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable(
            LabelResourceModel::MAIN_TABLE_NAME,
            RuleInterface::LABEL_ID,
            LabelInterface::LABEL_ID,
            self::LABEL_NAME,
            LabelInterface::NAME
        );
        parent::_renderFiltersBefore();
    }
}
