<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 */
namespace MageCloud\IndexManagement\Controller\Adminhtml\Indexer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Indexer\Controller\Adminhtml\Indexer;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Reindex
 * @package MageCloud\Base\Controller\Adminhtml\Indexer
 */
class Reindex extends \MageCloud\IndexManagement\Controller\Adminhtml\Indexer\Indexer
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var string
     */
    protected $_indexerListPath = 'indexer/indexer/list';

    /**
     * Reindex constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_objectManager = $context->getObjectManager();
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $indexerIds = $this->getRequest()->getParam('indexer_ids');

        if (empty($indexerIds)) {
            // reindex all indexer action
            $indexersData = $this->getAllIndexers();
            $indexerIdsData = [];
            foreach ($indexersData as $indexer) {
                if ($indexer && $indexer->getId()) {
                    $indexerIdsData[] = $indexer->getId();
                }
            }

            if (empty($indexerIdsData)) {
                $this->messageManager->addError(__('Sorry, we can\'t find indexers data'));
            } else {
                $result = $this->_doReindex($indexerIdsData);
                if (is_array($result)) {
                    foreach ($result as $message) {
                        $this->messageManager->addSuccess(__($message));
                    }
                } else {
                    $this->messageManager->addError($result);
                }
            }
        } else {
            // reindex specific indexer action
            if (!is_array($indexerIds)) {
                $this->messageManager->addError(__('Please select indexer(s)!'));
            } else {
                $result = $this->_doReindex($indexerIds);
                if (is_array($result)) {
                    foreach ($result as $message) {
                        $this->messageManager->addSuccess(__($message));
                    }
                } else {
                    if (is_string($result)) {
                        $this->messageManager->addError($result);
                    }
                }
            }
        }

        return $resultRedirect->setPath($this->_indexerListPath, ['_current' => true]);
    }

    /**
     * @param $indexData
     * @return array|\Magento\Framework\Phrase|string
     */
    protected function _doReindex($indexData)
    {
        if (!$indexData) {
            $errorMessage = 'Sorry, we can\'t find indexers data!';
            return $errorMessage;
        }

        $result = [];
        try {
            foreach ($indexData as $indexerId) {
                $startTime = microtime(true);
                /** @var \Magento\Framework\Indexer\IndexerInterface $indexModel */
                $indexModel = $this->_objectManager->get('Magento\Framework\Indexer\IndexerRegistry')->get($indexerId);
                // regenerate full index
                $indexModel->reindexAll();
                $resultTime = microtime(true) - $startTime;
                $result[] = $indexModel->getTitle() . ' index has been rebuilt successfully in '
                    . gmdate('H:i:s', $resultTime);
            }
            return $result;
        } catch (LocalizedException $e) {
            $errorMessage = 'We couldn\'t rebuild indexer(s). Error: ' . $e->getMessage();
            return (string)$errorMessage;
        }
    }

    /**
     * @return mixed
     */
    protected function getAllIndexers()
    {
        $collectionFactory = $this->_objectManager->create('Magento\Indexer\Model\Indexer\CollectionFactory');
        return $collectionFactory->create()->getItems();
    }
}