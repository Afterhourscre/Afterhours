<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\ResourceModel;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\RuleInterfaceFactory;
use Aheadworks\Acr\Api\Data\RuleSearchResultsInterface;
use Aheadworks\Acr\Api\Data\RuleSearchResultsInterfaceFactory;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Model\ResourceModel\Rule\Collection as RuleCollection;
use Aheadworks\Acr\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class RuleRepository
 * @package Aheadworks\Acr\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RuleRepository implements RuleRepositoryInterface
{
    /**
     * @var RuleInterface[]
     */
    private $instances = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleFactory;

    /**
     * @var RuleSearchResultsInterfaceFactory
     */
    private $ruleSearchResultsFactory;

    /**
     * @var RuleCollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param EntityManager $entityManager
     * @param RuleInterfaceFactory $ruleFactory
     * @param RuleSearchResultsInterfaceFactory $ruleSearchResultsFactory
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        EntityManager $entityManager,
        RuleInterfaceFactory $ruleFactory,
        RuleSearchResultsInterfaceFactory $ruleSearchResultsFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->entityManager = $entityManager;
        $this->ruleFactory = $ruleFactory;
        $this->ruleSearchResultsFactory = $ruleSearchResultsFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(RuleInterface $rule)
    {
        try {
            $this->entityManager->save($rule);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        unset($this->instances[$rule->getId()]);
        return $this->get($rule->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function get($ruleId)
    {
        if (!isset($this->instances[$ruleId])) {
            /** @var RuleInterface $rule */
            $rule = $this->ruleFactory->create();
            $this->entityManager->load($rule, $ruleId);
            if (!$rule->getId()) {
                throw NoSuchEntityException::singleField('id', $ruleId);
            }
            $this->instances[$ruleId] = $rule;
        }
        return $this->instances[$ruleId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var RuleSearchResultsInterface $searchResults */
        $searchResults = $this->ruleSearchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var RuleCollection $collection */
        $collection = $this->ruleCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, RuleInterface::class);

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $collection
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $rules = [];
        /** @var \Aheadworks\Acr\Model\Rule $ruleModel */
        foreach ($collection as $ruleModel) {
            /** @var RuleInterface $rule */
            $rule = $this->ruleFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $rule,
                $ruleModel->getData(),
                RuleInterface::class
            );
            $rules[] = $rule;
        }

        $searchResults->setItems($rules);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(RuleInterface $rule)
    {
        return $this->deleteById($rule->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($ruleId)
    {
        /** @var RuleInterface $rule */
        $rule = $this->ruleFactory->create();
        $this->entityManager->load($rule, $ruleId);
        if (!$rule->getId()) {
            throw NoSuchEntityException::singleField('ruleId', $ruleId);
        }
        $this->entityManager->delete($rule);
        unset($this->instances[$ruleId]);
        return true;
    }
}
