<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MagentoChatSystem
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MagentoChatSystem\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Webkul\MagentoChatSystem\Api\SaveCustomerInterface;
use Webkul\MagentoChatSystem\Model\CustomerDataRepository as CustomerDataRepository;
use Webkul\MagentoChatSystem\Api\Data\CustomerDataInterfaceFactory;
use Webkul\MagentoChatSystem\Model\ResourceModel\CustomerData\CollectionFactory;
use Magento\Framework\Api\DataObjectHelper;

class CustomerLogOutObserver implements ObserverInterface
{
    /**
     * @var Items
     */
    protected $dataRepository;

    /**
     * @var CollectionFactory
     */
    protected $_dataCollection;


    /** @var DataObjectHelper  */
    protected $dataObjectHelper;

    /** @var PreorderItemsInterfaceFactory  */

    protected $customerDataFactory;
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Webkul\MarketplacePreorder\Helper\Data $preorderHelper
     * @param ItemsRepository $itemsRepository
     * @param CustomerDataInterfaceFactory $preorderItemsFactory
     * @param CollectionFactory $completeCollection
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        CustomerDataRepository $dataRepository,
        CustomerDataInterfaceFactory $customerDataFactory,
        CollectionFactory $dataCollection,
        DataObjectHelper $dataObjectHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->dataRepository = $dataRepository;
        $this->customerDataFactory = $customerDataFactory;
        $this->_dataCollection = $dataCollection;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerSession = $customerSession;
        $this->_objectManager = $objectManager;
    }

    public function execute(EventObserver $observer)
    {
        $customerId = $observer->getCustomer()->getId();
        $customer = $this->_objectManager->create(
            'Magento\Customer\Model\Customer'
        )->load($customerId);
        if ($customer) {
            $chatCustomerCollection = $this->_dataCollection->create()
                ->addFieldToFilter('customer_id', ['eq' => $customer->getId()]);

            if (count($chatCustomerCollection)) {
                $entityId = 0;
                foreach ($chatCustomerCollection as $dataCollection) {
                    $entityId = $dataCollection->getEntityId();
                }
                $savedData = $this->dataRepository->getById($entityId);
                $savedData = (array) $savedData->getData();
                $customerData = array_merge(
                    $savedData,
                    ['customer_id' => $customer->getId(),'chat_status' => 0]
                );
                $customerData['entity_id'] = $entityId;
                $dataObject = $this->customerDataFactory->create();

                $this->dataObjectHelper->populateWithArray(
                    $dataObject,
                    $customerData,
                    '\Webkul\MagentoChatSystem\Api\Data\CustomerDataInterface'
                );
                $this->removeAssignedAgent($customerId);
                try {
                    $this->dataRepository->save($dataObject);
                } catch (\Exception $e) {
                    $customerData['error'] = true;
                    throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
                }
            }
        }
    }

    protected function removeAssignedAgent($customerId)
    {
        $assignedAgent = $this->_objectManager->create(
            'Webkul\MagentoChatSystem\Model\AssignedChat'
        )->getCollection()
        ->addFieldToFilter('customer_id', ['eq' => $customerId])
        ->addFieldToFilter('chat_status', ['eq' => 1]);

        if ($assignedAgent->getSize()) {
            $assignedId = $assignedAgent->getFirstItem()->getEntityId();
            $agentId = $assignedAgent->getFirstItem()->getAgentId();

            $assignModel = $this->_objectManager->create(
                'Webkul\MagentoChatSystem\Model\AssignedChat'
            )->load($assignedId);
            $assignModel->setChatStatus(0);
            $assignModel->setId($assignedId)->save();

            $totalAssignedChat = $this->_objectManager->create(
                'Webkul\MagentoChatSystem\Model\TotalAssignedChat'
            )->getCollection()
            ->addFieldToFilter('agent_id', ['eq' => $agentId]);
            $totalAssignId = $totalAssignedChat->getFirstItem()->getEntityId();
            $totalActiveChat = $totalAssignedChat->getFirstItem()->getTotalActiveChat();
            $totalAssignedModel = $this->_objectManager->create(
                'Webkul\MagentoChatSystem\Model\TotalAssignedChat'
            )->load($totalAssignId);
            $totalAssignedModel->setTotalActiveChat($totalActiveChat-1);
            $totalAssignedModel->setId($totalAssignId)->save();
        }
    }
}
