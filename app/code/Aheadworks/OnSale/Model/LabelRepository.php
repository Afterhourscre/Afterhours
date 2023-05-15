<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model;

use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Aheadworks\OnSale\Api\Data\LabelInterface;
use Aheadworks\OnSale\Api\Data\LabelInterfaceFactory;
use Aheadworks\OnSale\Api\Data\LabelSearchResultsInterface;
use Aheadworks\OnSale\Api\Data\LabelSearchResultsInterfaceFactory;
use Aheadworks\OnSale\Model\ResourceModel\Label as LabelResourceModel;
use Aheadworks\OnSale\Model\ResourceModel\Label\CollectionFactory as LabelCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class LabelRepository
 *
 * @package Aheadworks\OnSale\Model
 */
class LabelRepository implements LabelRepositoryInterface
{
    /**
     * @var LabelResourceModel
     */
    private $resource;

    /**
     * @var LabelInterfaceFactory
     */
    private $labelInterfaceFactory;

    /**
     * @var LabelCollectionFactory
     */
    private $labelCollectionFactory;

    /**
     * @var LabelSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param LabelResourceModel $resource
     * @param LabelInterfaceFactory $labelInterfaceFactory
     * @param LabelCollectionFactory $labelCollectionFactory
     * @param LabelSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        LabelResourceModel $resource,
        LabelInterfaceFactory $labelInterfaceFactory,
        LabelCollectionFactory $labelCollectionFactory,
        LabelSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->labelInterfaceFactory = $labelInterfaceFactory;
        $this->labelCollectionFactory = $labelCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(LabelInterface $label)
    {
        try {
            $this->resource->save($label);
            $this->registry[$label->getLabelId()] = $label;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $label;
    }

    /**
     * {@inheritdoc}
     */
    public function get($labelId)
    {
        if (!isset($this->registry[$labelId])) {
            /** @var LabelInterface $label */
            $label = $this->labelInterfaceFactory->create();
            $this->resource->load($label, $labelId);
            if (!$label->getLabelId()) {
                throw NoSuchEntityException::singleField('label_id', $labelId);
            }
            $this->registry[$labelId] = $label;
        }
        return $this->registry[$labelId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        /** @var \Aheadworks\OnSale\Model\ResourceModel\Label\Collection $collection */
        $collection = $this->labelCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, LabelInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var LabelSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Label $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(LabelInterface $label)
    {
        try {
            $this->resource->delete($label);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        if (isset($this->registry[$label->getLabelId()])) {
            unset($this->registry[$label->getLabelId()]);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($labelId)
    {
        return $this->delete($this->get($labelId));
    }

    /**
     * Retrieves data object using model
     *
     * @param Label $model
     * @return LabelInterface
     */
    private function getDataObject($model)
    {
        /** @var LabelInterface $object */
        $object = $this->labelInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $this->dataObjectProcessor->buildOutputDataArray($model, LabelInterface::class),
            LabelInterface::class
        );
        return $object;
    }
}
