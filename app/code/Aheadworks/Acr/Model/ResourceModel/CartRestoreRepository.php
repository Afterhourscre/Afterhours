<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\ResourceModel;

use Aheadworks\Acr\Api\Data\CartRestoreInterface;
use Aheadworks\Acr\Api\Data\CartRestoreInterfaceFactory;
use Aheadworks\Acr\Api\Data\CartRestoreSearchResultsInterface;
use Aheadworks\Acr\Api\Data\CartRestoreSearchResultsInterfaceFactory;
use Aheadworks\Acr\Api\CartRestoreRepositoryInterface;
use Aheadworks\Acr\Model\ResourceModel\CartRestore\Collection as CartRestoreCollection;
use Aheadworks\Acr\Model\ResourceModel\CartRestore\CollectionFactory as CartRestoreCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class CartRestoreRepository
 * @package Aheadworks\Acr\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartRestoreRepository implements CartRestoreRepositoryInterface
{
    /**
     * @var CartRestoreInterface[]
     */
    private $instances = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CartRestoreInterfaceFactory
     */
    private $cartRestoreFactory;

    /**
     * @var CartRestoreSearchResultsInterfaceFactory
     */
    private $cartRestoreSearchResultsFactory;

    /**
     * @var CartRestoreCollectionFactory
     */
    private $cartRestoreCollectionFactory;

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
     * @param CartRestoreInterfaceFactory $CartRestoreFactory
     * @param CartRestoreSearchResultsInterfaceFactory $CartRestoreSearchResultsFactory
     * @param CartRestoreCollectionFactory $CartRestoreCollectionFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        EntityManager $entityManager,
        CartRestoreInterfaceFactory $cartRestoreFactory,
        CartRestoreSearchResultsInterfaceFactory $cartRestoreSearchResultsFactory,
        CartRestoreCollectionFactory $cartRestoreCollectionFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->entityManager = $entityManager;
        $this->cartRestoreFactory = $cartRestoreFactory;
        $this->cartRestoreSearchResultsFactory = $cartRestoreSearchResultsFactory;
        $this->cartRestoreCollectionFactory = $cartRestoreCollectionFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CartRestoreInterface $CartRestore)
    {
        try {
            $this->entityManager->save($CartRestore);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        unset($this->instances[$CartRestore->getId()]);
        return $this->get($CartRestore->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function get($CartRestoreId)
    {
        if (!isset($this->instances[$CartRestoreId])) {
            /** @var CartRestoreInterface $CartRestore */
            $CartRestore = $this->cartRestoreFactory->create();
            $this->entityManager->load($CartRestore, $CartRestoreId);
            if (!$CartRestore->getId()) {
                throw NoSuchEntityException::singleField('id', $CartRestoreId);
            }
            $this->instances[$CartRestoreId] = $CartRestore;
        }
        return $this->instances[$CartRestoreId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByCode($cartRestoreCode)
    {
        /** @var CartRestoreInterface $CartRestore */
        $CartRestore = $this->cartRestoreFactory->create();
        $CartRestore->load($cartRestoreCode, CartRestoreInterface::RESTORE_CODE);
        if (!$CartRestore->getId()) {
            throw NoSuchEntityException::singleField(CartRestoreInterface::RESTORE_CODE, $cartRestoreCode);
        }
        return $CartRestore;
    }

    /**
     * {@inheritdoc}
     */
    public function getByEventHistoryId($eventHistoryId)
    {
        /** @var CartRestoreInterface $CartRestore */
        $CartRestore = $this->cartRestoreFactory->create();
        $CartRestore->load($eventHistoryId, CartRestoreInterface::EVENT_HISTORY_ID);
        if (!$CartRestore->getId()) {
            $CartRestore = null;
        }
        return $CartRestore;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CartRestoreSearchResultsInterface $searchResults */
        $searchResults = $this->cartRestoreSearchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var CartRestoreCollection $collection */
        $collection = $this->cartRestoreCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, CartRestoreInterface::class);

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
        /** @var \Aheadworks\Acr\Model\CartRestore $CartRestoreModel */
        foreach ($collection as $CartRestoreModel) {
            /** @var CartRestoreInterface $CartRestore */
            $CartRestore = $this->cartRestoreFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $CartRestore,
                $CartRestoreModel->getData(),
                CartRestoreInterface::class
            );
            $cartHistories[] = $CartRestore;
        }

        $searchResults->setItems($cartHistories);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CartRestoreInterface $CartRestore)
    {
        return $this->deleteById($CartRestore->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($CartRestoreId)
    {
        /** @var CartRestoreInterface $CartRestore */
        $CartRestore = $this->cartRestoreFactory->create();
        $this->entityManager->load($CartRestore, $CartRestoreId);
        if (!$CartRestore->getId()) {
            throw NoSuchEntityException::singleField('id', $CartRestoreId);
        }
        $this->entityManager->delete($CartRestore);
        unset($this->instances[$CartRestoreId]);
        return true;
    }
}
