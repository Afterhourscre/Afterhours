<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Model\ResourceModel;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Api\Data\QueueInterfaceFactory;
use Aheadworks\Acr\Api\Data\QueueSearchResultsInterface;
use Aheadworks\Acr\Api\Data\QueueSearchResultsInterfaceFactory;
use Aheadworks\Acr\Api\QueueRepositoryInterface;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Aheadworks\Acr\Model\ResourceModel\Queue as QueueResource;
use Aheadworks\Acr\Model\ResourceModel\Queue\Collection as QueueCollection;
use Aheadworks\Acr\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class QueueRepository
 * @package Aheadworks\Acr\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QueueRepository implements QueueRepositoryInterface
{
    /**
     * @var QueueInterface[]
     */
    private $instances = [];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var QueueInterfaceFactory
     */
    private $queueFactory;

    /**
     * @var QueueResource
     */
    private $queueResource;

    /**
     * @var QueueSearchResultsInterfaceFactory
     */
    private $queueSearchResultsFactory;

    /**
     * @var QueueCollectionFactory
     */
    private $queueCollectionFactory;

    /**
     * @var CartHistoryRepositoryInterface
     */
    private $cartHistoryRepository;

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
     * @param QueueInterfaceFactory $queueFactory
     * @param Queue $queueResource
     * @param QueueSearchResultsInterfaceFactory $queueSearchResultsFactory
     * @param QueueCollectionFactory $queueCollectionFactory
     * @param CartHistoryRepositoryInterface $cartHistoryRepository
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        EntityManager $entityManager,
        QueueInterfaceFactory $queueFactory,
        QueueResource $queueResource,
        QueueSearchResultsInterfaceFactory $queueSearchResultsFactory,
        QueueCollectionFactory $queueCollectionFactory,
        CartHistoryRepositoryInterface $cartHistoryRepository,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->entityManager = $entityManager;
        $this->queueFactory = $queueFactory;
        $this->queueResource = $queueResource;
        $this->queueSearchResultsFactory = $queueSearchResultsFactory;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->cartHistoryRepository = $cartHistoryRepository;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QueueInterface $queue)
    {
        try {
            $this->entityManager->save($queue);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        unset($this->instances[$queue->getId()]);
        return $this->get($queue->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function get($queueId)
    {
        if (!isset($this->instances[$queueId])) {
            /** @var QueueInterface $queue */
            $queue = $this->queueFactory->create();
            $this->entityManager->load($queue, $queueId);
            if (!$queue->getId()) {
                throw NoSuchEntityException::singleField('id', $queueId);
            }
            $this->instances[$queueId] = $queue;
        }
        return $this->instances[$queueId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var QueueSearchResultsInterface $searchResults */
        $searchResults = $this->queueSearchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var QueueCollection $collection */
        $collection = $this->queueCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, QueueInterface::class);

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

        $queues = [];
        /** @var \Aheadworks\Acr\Model\Queue $queueModel */
        foreach ($collection as $queueModel) {
            /** @var QueueInterface $queue */
            $queue = $this->queueFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $queue,
                $queueModel->getData(),
                QueueInterface::class
            );
            $queues[] = $queue;
        }

        $searchResults->setItems($queues);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(QueueInterface $queue)
    {
        return $this->deleteById($queue->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($queueId)
    {
        /** @var QueueInterface $queue */
        $queue = $this->queueFactory->create();
        $this->entityManager->load($queue, $queueId);
        if (!$queue->getId()) {
            throw NoSuchEntityException::singleField('queueId', $queueId);
        }
        if ($queue->getCartHistoryId()) {
            /** @var QueueCollection $collection */
            $collection = $this->queueCollectionFactory->create();
            $collection->addFilter(QueueInterface::CART_HISTORY_ID, $queue->getCartHistoryId());

            if ($collection->getSize() == 1) {
                try {
                    $this->cartHistoryRepository->deleteById($queue->getCartHistoryId());
                } catch (NoSuchEntityException $e) {
                    // do nothing
                }
            }
        }
        $this->entityManager->delete($queue);
        unset($this->instances[$queueId]);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByCartHistoryId($cartHistoryId)
    {
        $this->queueResource->deleteItemsByCartHistory($cartHistoryId);
        return true;
    }
}
