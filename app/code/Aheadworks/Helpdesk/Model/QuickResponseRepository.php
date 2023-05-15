<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Model;

use Aheadworks\Helpdesk\Api\QuickResponseRepositoryInterface;
use Aheadworks\Helpdesk\Api\Data\QuickResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Helpdesk\Model\QuickResponse as QuickResponseModel;
use Aheadworks\Helpdesk\Api\Data\QuickResponseInterfaceFactory;
use Aheadworks\Helpdesk\Api\Data\QuickResponseSearchResultsInterface;
use Aheadworks\Helpdesk\Api\Data\QuickResponseSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Store\Model\Store;
use Aheadworks\Helpdesk\Model\ResourceModel\QuickResponse\CollectionFactory as QuickResponseCollectionFactory;

/**
 * Class QuickResponseRepository
 *
 * @package Aheadworks\Helpdesk\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QuickResponseRepository implements QuickResponseRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var QuickResponseFactory
     */
    private $quickResponseFactory;

    /**
     * @var QuickResponseInterfaceFactory
     */
    private $quickResponseDataFactory;

    /**
     * @var QuickResponseSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var QuickResponseCollectionFactory
     */
    private $quickResponseCollectionFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param EntityManager $entityManager
     * @param QuickResponseFactory $quickResponseFactory
     * @param QuickResponseInterfaceFactory $quickResponseDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param QuickResponseSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param QuickResponseCollectionFactory $quickResponseCollectionFactory
     */
    public function __construct(
        EntityManager $entityManager,
        QuickResponseFactory $quickResponseFactory,
        QuickResponseInterfaceFactory $quickResponseDataFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        QuickResponseSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        QuickResponseCollectionFactory $quickResponseCollectionFactory
    ) {
        $this->entityManager = $entityManager;
        $this->quickResponseFactory = $quickResponseFactory;
        $this->quickResponseDataFactory =$quickResponseDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->quickResponseCollectionFactory = $quickResponseCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QuickResponseInterface $quickResponse)
    {
        /** @var \Aheadworks\Helpdesk\Model\QuickResponse $quickResponseModel */
        $quickResponseModel = $this->quickResponseFactory->create();
        if ($quickResponseId = $quickResponse->getId()) {
            $arguments = ['store_id' => Store::DEFAULT_STORE_ID];
            $this->entityManager->load($quickResponseModel, $quickResponseId, $arguments);
        }
        $quickResponseModel->setOrigData(null, $quickResponseModel->getData());
        $this->dataObjectHelper->populateWithArray(
            $quickResponseModel,
            $this->dataObjectProcessor->buildOutputDataArray($quickResponse, QuickResponseInterface::class),
            QuickResponseInterface::class
        );
        $this->entityManager->save($quickResponseModel);
        $quickResponse = $this->getQuickResponseDataObject($quickResponseModel);
        $this->registry[$quickResponse->getId()] = $quickResponse;

        return $quickResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function get($quickResponseId, $storeId = null)
    {
        if (!isset($this->registry[$quickResponseId])) {
            /** @var QuickResponseInterface $code */
            $quickResponse = $this->quickResponseDataFactory->create();
            $storeId = $storeId ? : Store::DEFAULT_STORE_ID;
            $arguments = ['store_id' => $storeId];
            $this->entityManager->load($quickResponse, $quickResponseId, $arguments);
            if (!$quickResponse->getId()) {
                throw NoSuchEntityException::singleField('quickResponseId', $quickResponseId);
            }
            $this->registry[$quickResponseId] = $quickResponse;
        }
        return $this->registry[$quickResponseId];
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var QuickResponseSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var \Aheadworks\Helpdesk\Model\ResourceModel\QuickResponse\Collection $collection */
        $collection = $this->quickResponseCollectionFactory->create();
        $this->extensionAttributesJoinProcessor->process($collection, QuickResponseInterface::class);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType()
                    ? $filter->getConditionType()
                    : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $storeId = $storeId ? : Store::DEFAULT_STORE_ID;
        $collection
            ->setStoreId($storeId)
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $quickResponses = [];
        /** @var QuickResponseModel $quickResponseModel */
        foreach ($collection as $quickResponseModel) {
            $quickResponses[] = $this->getQuickResponseDataObject($quickResponseModel);
        }
        $searchResults->setItems($quickResponses);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(QuickResponseInterface $quickResponse)
    {
        return $this->deleteById($quickResponse->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($quickResponseId)
    {
        $quickResponse = $this->get($quickResponseId);
        $this->entityManager->delete($quickResponse);
        if (isset($this->registry[$quickResponseId])) {
            unset($this->registry[$quickResponseId]);
        }
        return true;
    }

    /**
     * Retrieves quick response data object using code model
     *
     * @param QuickResponseModel $quickResponse
     * @return QuickResponseInterface
     */
    private function getQuickResponseDataObject(QuickResponse $quickResponse)
    {
        /** @var QuickResponseInterface $quickResponseDataObject */
        $quickResponseDataObject = $this->quickResponseDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quickResponseDataObject,
            $this->dataObjectProcessor->buildOutputDataArray($quickResponse, QuickResponseInterface::class),
            QuickResponseInterface::class
        );
        return $quickResponseDataObject;
    }
}
