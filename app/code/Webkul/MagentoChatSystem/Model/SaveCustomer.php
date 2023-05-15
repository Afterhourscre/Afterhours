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

use Webkul\MagentoChatSystem\Api\SaveCustomerInterface;
use Webkul\MagentoChatSystem\Model\CustomerDataRepository as CustomerDataRepository;
use Webkul\MagentoChatSystem\Api\Data\CustomerDataInterfaceFactory;
use Webkul\MagentoChatSystem\Model\ResourceModel\CustomerData\CollectionFactory;
use Webkul\MagentoChatSystem\Model\ResourceModel\AssignedChat\CollectionFactory as AssignedChatCollection;
use Webkul\MagentoChatSystem\Model\ResourceModel\AgentData\CollectionFactory as AgentCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Webkul\MagentoChatSystem\Api\AgentDataRepositoryInterface;

class SaveCustomer implements SaveCustomerInterface
{

    /**
     * @var Items
     */
    protected $dataRepository;

    /**
     * @var CollectionFactory
     */
    protected $_dataCollection;

    /**
     * @var AssignedChatCollection
     */
    protected $assignedChatCollection;


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
     * View file system
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_viewFileSystem;

    /**
     * @param \Webkul\MarketplacePreorder\Helper\Data $preorderHelper
     * @param ItemsRepository $itemsRepository
     * @param CustomerDataInterfaceFactory $preorderItemsFactory
     * @param CollectionFactory $completeCollection
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        CustomerDataRepository $dataRepository,
        CustomerDataInterfaceFactory $customerDataFactory,
        CollectionFactory $dataCollection,
        AssignedChatCollection $assignedChatCollection,
        AgentCollectionFactory $agentFactoryCollection,
        AgentDataRepositoryInterface $agentRepository,
        DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\View\Asset\Repository $viewFileSystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->dataRepository = $dataRepository;
        $this->customerDataFactory = $customerDataFactory;
        $this->_dataCollection = $dataCollection;
        $this->assignedChatCollection = $assignedChatCollection;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerSession = $customerSession;
        $this->_date = $date;
        $this->_objectManager = $objectManager;
        $this->_viewFileSystem = $viewFileSystem;
        $this->agentFactoryCollection = $agentFactoryCollection;
        $this->agentRepository = $agentRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $email Users name.
     * @param int $agentId Agent Id.
     * @param string $agentUniqueId Users name.
     * @return string Greeting message with users name.
     */
    public function save($message, $agentId, $agentUniqueId)
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $customer = $this->_objectManager->create(
            'Magento\Customer\Model\Customer'
        )->load($customerId);

        if ($customer) {
            $defaultImageUrl = $this ->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ).
            'chatsystem/default.png';

            $chatCustomerCollection = $this->_dataCollection->create()
                ->addFieldToFilter('customer_id', ['eq' => $customer->getId()]);


            if (count($chatCustomerCollection)) {
                $entityId = 0;
                foreach ($chatCustomerCollection as $dataCollection) {
                    $entityId = $dataCollection->getEntityId();
                    $uniqueId = $dataCollection->getUniqueId();
                }

                $assignedAgentCollection = $this->assignedChatCollection->create()
                    ->addFieldToFilter('customer_id', ['eq' => $customerId])
                    ->addFieldToFilter('chat_status', ['eq' => 1]);
                
                $savedData = $this->dataRepository->getById($entityId);
                $savedData = (array) $savedData->getData();
                $customerData = array_merge(
                    $savedData,
                    ['customer_id' => $customer->getId(),'chat_status' => 1]
                );
                $customerData['alreadyAssigned'] = false;
                if ($assignedAgentCollection->getSize()) {
                    $assignedModel = $assignedAgentCollection->getFirstItem();
                    $agentModel = $this->agentRepository->getByAgentId($assignedModel->getAgentId());
                    $customerData['alreadyAssigned'] = true;
                    $customerData['agent_unique_id'] = $agentModel->getAgentUniqueId();
                    $customerData['agent_id'] = $agentModel->getAgentId();
                    $customerData['agent_name'] =  $agentModel->getAgentName();
                    $customerData['email'] = $agentModel->getAgentEmail();
                }
                if ($chatCustomerCollection->getFirstItem()->getImage()) {
                    $defaultImageUrl = $this ->storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ).
                    'chatsystem/profile/'
                    .$customer->getId().'/'.$chatCustomerCollection->getFirstItem()->getImage();
                }
                
                $customerData['entity_id'] = $entityId;
                $customerData['unique_id'] = $uniqueId;
            } else {
                $customerData = [
                    'customer_id' => $customer->getId(),
                    'chat_status' => 1,
                    'unique_id' => $this->generateUniqueId(),
                    'alreadyAssigned' => false
                ];
            }
            $dataObject = $this->customerDataFactory->create();

            $this->dataObjectHelper->populateWithArray(
                $dataObject,
                $customerData,
                '\Webkul\MagentoChatSystem\Api\Data\CustomerDataInterface'
            );
            $customerData['customer_name'] = $customer->getName();
            $customerData['customer_email'] = $customer->getEmail();
            $customerData['profileImageUrl'] = $defaultImageUrl;
            try {
                $this->dataRepository->save($dataObject);
                $customerData['message'] = $message;
                $customerData['error'] = false;
            } catch (\Exception $e) {
                $customerData['error'] = true;
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
            return json_encode($customerData);
        }
    }
    
    /**
     * Generate unique id
     *
     * @return string
     */
    public function generateUniqueId()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        $id = 'wk'.implode($pass);
        return $id;
    }
}
