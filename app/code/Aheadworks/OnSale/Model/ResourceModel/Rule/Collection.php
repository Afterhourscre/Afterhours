<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\ResourceModel\Rule;

use Aheadworks\OnSale\Model\ResourceModel\AbstractCollection;
use Aheadworks\OnSale\Model\ResourceModel\Rule as ResourceRule;
use Aheadworks\OnSale\Model\Rule as RuleModel;
use Aheadworks\OnSale\Model\ResourceModel\Rule as RuleResourceModel;
use Magento\Store\Model\Store;
use Aheadworks\OnSale\Model\Rule\LabelText\StoreResolver as LabelTextStoreResolver;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\OnSale\Api\Data\RuleInterface;
use Aheadworks\OnSale\Api\Data\LabelTextStoreValueInterface;
use Aheadworks\OnSale\Model\Rule\ObjectDataProcessor as RuleDataProcessor;

/**
 * Class Collection
 *
 * @package Aheadworks\OnSale\Model\ResourceModel\Rule
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = RuleInterface::RULE_ID;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var LabelTextStoreResolver
     */
    protected $labelTextStoreResolver;

    /**
     * @var RuleDataProcessor
     */
    protected $ruleDataProcessor;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(RuleModel::class, ResourceRule::class);
    }

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param LabelTextStoreResolver $labelTextStoreResolver
     * @param RuleDataProcessor $ruleDataProcessor
     * @param null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        LabelTextStoreResolver $labelTextStoreResolver,
        RuleDataProcessor $ruleDataProcessor,
        $connection = null,
        AbstractDb $resource = null
    ) {
        $this->labelTextStoreResolver = $labelTextStoreResolver;
        $this->ruleDataProcessor = $ruleDataProcessor;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, [RuleInterface::WEBSITE_IDS])) {
            $this->addFilter($field, $condition, 'public');
            return $this;
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFilterToMap(RuleInterface::RULE_ID, 'main_table.rule_id');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            RuleResourceModel::WEBSITE_TABLE_NAME,
            RuleInterface::RULE_ID,
            RuleInterface::RULE_ID,
            'website_id',
            RuleInterface::WEBSITE_IDS
        );
        $this->attachRelationTable(
            RuleResourceModel::FRONTEND_LABEL_TEXT_TABLE_NAME,
            RuleInterface::RULE_ID,
            RuleInterface::RULE_ID,
            [
                LabelTextStoreValueInterface::STORE_ID,
                LabelTextStoreValueInterface::VALUE_LARGE,
                LabelTextStoreValueInterface::VALUE_MEDIUM,
                LabelTextStoreValueInterface::VALUE_SMALL
            ],
            RuleInterface::FRONTEND_LABEL_TEXT_STORE_VALUES
        );

        /** @var RuleModel $item */
        foreach ($this as $item) {
            $item->setData(
                RuleInterface::FRONTEND_LABEL_TEXT,
                $this->labelTextStoreResolver->getLabelTextAsArray(
                    $item->getFrontendLabelTextStoreValues(),
                    $this->getStoreId()
                )
            );
            $this->ruleDataProcessor->prepareDataAfterLoad($item);
        }
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable(
            RuleResourceModel::WEBSITE_TABLE_NAME,
            RuleInterface::RULE_ID,
            RuleInterface::RULE_ID,
            RuleInterface::WEBSITE_IDS,
            'website_id'
        );
        parent::_renderFiltersBefore();
    }

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Get store ID
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId ? $this->storeId : Store::DEFAULT_STORE_ID;
    }
}
