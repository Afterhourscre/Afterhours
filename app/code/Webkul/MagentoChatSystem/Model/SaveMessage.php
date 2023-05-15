<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MagentoChatSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MagentoChatSystem\Model;

use Webkul\MagentoChatSystem\Api\SaveMessageInterface;
use Webkul\MagentoChatSystem\Api\Data\MessageInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Webkul\MagentoChatSystem\Model\ResourceModel\CustomerData\CollectionFactory;
use Webkul\MagentoChatSystem\Helper\Data;
use Webkul\MagentoChatSystem\Api\AgentDataRepositoryInterface;
 
class SaveMessage implements SaveMessageInterface
{
        /**
         * @var Items
         */
    protected $messageRepository;

    /**
     * @var CollectionFactory
     */
    protected $_dataCollection;


    /** @var DataObjectHelper  */
    protected $dataObjectHelper;

    /** @var PreorderItemsInterfaceFactory  */

    protected $messageFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var CollectionFactory
     */
    private $_chatCustomerCollection;

    /**
     * @param \Webkul\MarketplacePreorder\Helper\Data $preorderHelper
     * @param ItemsRepository $itemsRepository
     * @param MessageInterfaceFactory $preorderItemsFactory
     * @param CollectionFactory $completeCollection
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        MessageRepository $messageRepository,
        MessageInterfaceFactory $messageFactory,
        AgentDataRepositoryInterface $agentRepository,
        DataObjectHelper $dataObjectHelper,
        CollectionFactory $dataCollection,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Url\EncoderInterface $encoder,
        Data $helper
    ) {
        $this->messageRepository = $messageRepository;
        $this->messageFactory = $messageFactory;
        $this->agentRepository = $agentRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerSession = $customerSession;
        $this->_date = $date;
        $this->_chatCustomerCollection = $dataCollection;
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->encoder = $encoder;
    }

   /**
    * Returns greeting message to user
    *
    * @api
    * @param int $senderId
    * @param int $receiverId
    * @param string $receiverUniqueId
    * @param string $message
    * @param string $dateTime
    * @return string Greeting message with users name.
    */
    public function saveMeassage($senderId, $receiverId, $receiverUniqueId, $message, $dateTime, $msgType = '')
    {
        $agentData = [];
        $customerId = $this->customerSession->getCustomer()->getId();
        $customer = $this->_objectManager->create(
            'Magento\Customer\Model\Customer'
        )->load($customerId);
        if ($customer) {
            $chatCustomerCollection = $this->_chatCustomerCollection->create()
                ->addFieldToFilter('customer_id', ['eq' => $customer->getId()]);

            $chatCustomerUniqueId = $chatCustomerCollection->getFirstItem()->getUniqueId();
            $agentModel = $this->_objectManager->create(
                'Webkul\MagentoChatSystem\Model\AgentData'
            )->getCollection()
            ->addFieldToFilter('agent_id', ['eq' => $receiverId])
            ->addFieldToFilter('chat_status', ['eq' => 1]);

            $error = false;
            $hasAgentNewValue = false;
            $agentName = $this->agentRepository->getByAgentId($receiverId)->getAgentName();
            if (!$agentModel->getSize()) {
                $assignNewAgent = $this->_objectManager->create('Webkul\MagentoChatSystem\Model\SaveAssignedChat');
                $data = $assignNewAgent->assignChat($customerId, $chatCustomerUniqueId);
                $agentData = (array)json_decode($data, true);

                $receiverId = $agentData['agent_id'];
                $receiverUniqueId = $agentData['agent_unique_id'];
                $agentName = $agentData['agent_name'];
                $error = $agentData['error'];
                $hasAgentNewValue = true;
            }
            if ($msgType == 'image' || $msgType == 'file') {
                $message = $this->encoder->encode(trim($message));
            }

            $messageData = [
                'sender_id' => $customerId,
                'sender_unique_id' => $chatCustomerUniqueId,
                'sender' => $customer->getName(),
                'receiver_id' => $receiverId,
                'receiver_unique_id' => $receiverUniqueId,
                'receiver' => $agentName,
                'message'   => $message,
                'date'  => $this->_date->gmtDate('Y-m-d H:i:s', $dateTime),
                'updateAgent' => $hasAgentNewValue,
                'errors' => $error

            ];
            array_merge($messageData, $agentData);
            if ((bool)$this->helper->getConfigData('chat_config', 'offline_message') && $error) {
                $messageData['msg'] = __(
                    'Agents are offline, send message we will revert you soon.'
                );
            } elseif ($error) {
                $messageData['msg'] = __(
                    'All agents are offline, try after sometime.'
                );
            }
            $dataObject = $this->messageFactory->create();
            
            $this->dataObjectHelper->populateWithArray(
                $dataObject,
                $messageData,
                '\Webkul\MagentoChatSystem\Api\Data\MessageInterface'
            );
            try {
                $this->messageRepository->save($dataObject);
            } catch (\Exception $e) {
                $messageData['errors'] = true;
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
            return json_encode($messageData);
        }
    }
}
