<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\ResourceModel;

use Aheadworks\Acr\Api\Data\CartHistoryInterface;
use Aheadworks\Acr\Api\Data\CartHistoryInterfaceFactory;
use Aheadworks\Acr\Api\Data\CartHistorySearchResultsInterface;
use Aheadworks\Acr\Api\Data\CartHistorySearchResultsInterfaceFactory;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Model\ResourceModel\CartHistory\Collection as CartHistoryCollection;
use Aheadworks\Acr\Model\ResourceModel\CartHistory\CollectionFactory as CartHistoryCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class CartHistoryRepository
 * @package Aheadworks\Acr\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartHistoryRepository implements CartHistoryRepositoryInterface
{
    /**
     * @var CartHistoryInterface[]
     */
    private $instances = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CartHistoryInterfaceFactory
     */
    private $cartHistoryFactory;

    /**
     * @var CartHistorySearchResultsInterfaceFactory
     */
    private $cartHistorySearchResultsFactory;

    /**
     * @var CartHistoryCollectionFactory
     */
    private $cartHistoryCollectionFactory;

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
     * @param CartHistoryInterfaceFactory $cartHistoryFactory
     * @param CartHistorySearchResultsInterfaceFactory $cartHistorySearchResultsFactory
     * @param CartHistoryCollectionFactory $cartHistoryCollectionFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        EntityManager $entityManager,
        CartHistoryInterfaceFactory $cartHistoryFactory,
        CartHistorySearchResultsInterfaceFactory $cartHistorySearchResultsFactory,
        CartHistoryCollectionFactory $cartHistoryCollectionFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->entityManager = $entityManager;
        $this->cartHistoryFactory = $cartHistoryFactory;
        $this->cartHistorySearchResultsFactory = $cartHistorySearchResultsFactory;
        $this->cartHistoryCollectionFactory = $cartHistoryCollectionFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CartHistoryInterface $cartHistory)
    {
        try {
            $this->entityManager->save($cartHistory);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        unset($this->instances[$cartHistory->getId()]);
        return $this->get($cartHistory->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartHistoryId)
    {
        if (!isset($this->instances[$cartHistoryId])) {
            /** @var CartHistoryInterface $cartHistory */
            $cartHistory = $this->cartHistoryFactory->create();
            $this->entityManager->load($cartHistory, $cartHistoryId);
            if (!$cartHistory->getId()) {
                throw NoSuchEntityException::singleField('id', $cartHistoryId);
            }
            $this->instances[$cartHistoryId] = $cartHistory;
        }
        return $this->instances[$cartHistoryId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CartHistorySearchResultsInterface $searchResults */
        $searchResults = $this->cartHistorySearchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var CartHistoryCollection $collection */
        $collection = $this->cartHistoryCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, CartHistoryInterface::class);

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

        $cartHistories = [];
        /** @var \Aheadworks\Acr\Model\CartHistory $cartHistoryModel */
        foreach ($collection as $cartHistoryModel) {
            /** @var CartHistoryInterface $cartHistory */
            $cartHistory = $this->cartHistoryFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $cartHistory,
                $cartHistoryModel->getData(),
                CartHistoryInterface::class
            );
            $cartHistories[] = $cartHistory;
        }

        $searchResults->setItems($cartHistories);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CartHistoryInterface $cartHistory)
    {
        return $this->deleteById($cartHistory->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cartHistoryId)
    {
        /** @var CartHistoryInterface $cartHistory */
        $cartHistory = $this->cartHistoryFactory->create();
        $this->entityManager->load($cartHistory, $cartHistoryId);
        if (!$cartHistory->getId()) {
            throw NoSuchEntityException::singleField('id', $cartHistoryId);
        }
        $this->entityManager->delete($cartHistory);
        unset($this->instances[$cartHistoryId]);
        return true;
    }
}
