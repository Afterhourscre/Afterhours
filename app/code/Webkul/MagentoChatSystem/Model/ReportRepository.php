<?php


namespace Webkul\MagentoChatSystem\Model;

use Webkul\MagentoChatSystem\Api\Data\ReportSearchResultsInterfaceFactory;
use Webkul\MagentoChatSystem\Api\ReportRepositoryInterface;
use Webkul\MagentoChatSystem\Model\ResourceModel\Report as ResourceReport;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Webkul\MagentoChatSystem\Api\Data\ReportInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Webkul\MagentoChatSystem\Model\ResourceModel\Report\CollectionFactory as ReportCollectionFactory;

class ReportRepository implements ReportRepositoryInterface
{

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    protected $reportCollectionFactory;

    private $storeManager;

    protected $resource;

    protected $searchResultsFactory;

    protected $dataReportFactory;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;
    protected $reportFactory;

    protected $extensionAttributesJoinProcessor;


    /**
     * @param ResourceReport $resource
     * @param ReportFactory $reportFactory
     * @param ReportInterfaceFactory $dataReportFactory
     * @param ReportCollectionFactory $reportCollectionFactory
     * @param ReportSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceReport $resource,
        ReportFactory $reportFactory,
        ReportInterfaceFactory $dataReportFactory,
        ReportCollectionFactory $reportCollectionFactory,
        ReportSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->reportFactory = $reportFactory;
        $this->reportCollectionFactory = $reportCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataReportFactory = $dataReportFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Webkul\MagentoChatSystem\Api\Data\ReportInterface $report
    ) {
        /* if (empty($report->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $report->setStoreId($storeId);
        } */
        
        $reportData = $this->extensibleDataObjectConverter->toNestedArray(
            $report,
            [],
            \Webkul\MagentoChatSystem\Api\Data\ReportInterface::class
        );
        
        $reportModel = $this->reportFactory->create()->setData($reportData);
        
        try {
            $this->resource->save($reportModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the report: %1',
                $exception->getMessage()
            ));
        }
        return $reportModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($reportId)
    {
        $report = $this->reportFactory->create();
        $this->resource->load($report, $reportId);
        if (!$report->getId()) {
            throw new NoSuchEntityException(__('Report with id "%1" does not exist.', $reportId));
        }
        return $report->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->reportCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Webkul\MagentoChatSystem\Api\Data\ReportInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Webkul\MagentoChatSystem\Api\Data\ReportInterface $report
    ) {
        try {
            $this->resource->delete($report);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Report: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($reportId)
    {
        return $this->delete($this->getById($reportId));
    }
}
