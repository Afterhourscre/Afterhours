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
namespace Webkul\MagentoChatSystem\Model;

use Webkul\MagentoChatSystem\Api\ChangeStatusInterface;
use Webkul\MagentoChatSystem\Model\CustomerDataRepository as CustomerDataRepository;
use Webkul\MagentoChatSystem\Api\Data\CustomerDataInterfaceFactory;
use Webkul\MagentoChatSystem\Model\ResourceModel\CustomerData\CollectionFactory;
use Magento\Framework\Api\DataObjectHelper;

class ChangeStatus implements ChangeStatusInterface
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
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

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
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->dataRepository = $dataRepository;
        $this->customerDataFactory = $customerDataFactory;
        $this->_dataCollection = $dataCollection;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerSession = $customerSession;
        $this->_date = $date;
        $this->_objectManager = $objectManager;
    }

    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $status Users name.
     * @return string Greeting message with users name.
     */
    public function changeStatus($status)
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $customer = $this->_objectManager->create(
            'Magento\Customer\Model\Customer'
        )->load($customerId);
        if ($customer) {
            $chatCustomerCollection = $this->_dataCollection->create()
                ->addFieldToFilter('customer_id', ['eq' => $customer->getId()]);

            if (count($chatCustomerCollection)) {
                $entityId = 0;
                if ($status == 0) {
                    $this->removeAssignedAgent();
                }

                foreach ($chatCustomerCollection as $dataCollection) {
                    $entityId = $dataCollection->getEntityId();
                }
                $savedData = $this->dataRepository->getById($entityId);
                $savedData = (array) $savedData->getData();
                $customerData = array_merge(
                    $savedData,
                    ['customer_id' => $customer->getId(),'chat_status' => $status]
                );
                $customerData['entity_id'] = $entityId;

                $dataObject = $this->customerDataFactory->create();

                $this->dataObjectHelper->populateWithArray(
                    $dataObject,
                    $customerData,
                    '\Webkul\MagentoChatSystem\Api\Data\CustomerDataInterface'
                );
                try {
                    $this->dataRepository->save($dataObject);
                    $customerData['message'] = 'status changed';
                    $customerData['error'] = false;
                } catch (\Exception $e) {
                    $customerData['error'] = true;
                    throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
                }
            }
            return json_encode($customerData);
        }
    }

    protected function removeAssignedAgent()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
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
