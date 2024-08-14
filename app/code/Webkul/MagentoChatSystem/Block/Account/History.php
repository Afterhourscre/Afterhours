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
namespace Webkul\MagentoChatSystem\Block\Account;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class History extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Webkul\MagentoChatSystem\Model\ChatDataConfigProvider
     */
    protected $configProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    protected $history;

    /**
     * Agreement constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Artera\Privacy\Model\Agreement                  $agreement
     * @param \Artera\Privacy\Model\Page                       $page
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Url\DecoderInterface $urlDecoder,
        \Magento\Framework\Filesystem $filesystem,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerSession = $customerSession;
        $this->objectManager = $objectManager;
        $this->filesystem = $filesystem;
        $this->urlDecoder = $urlDecoder;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Chat History'));
    }

    /**
     * Message collection
     *
     * @return \Webkul\MagentoChatSystem\Model\ResourceModel\Message\Collection
     */
    public function getHistoryCollection()
    {
        if (!$this->history) {
            $paramData = $this->getRequest()->getParams();
            $agentName = '';
            $msgDate = '';
            if (isset($paramData['agent'])) {
                $agentName = $paramData['agent'];
            }
            if (isset($paramData['msg_date'])) {
                $msgDate = $paramData['msg_date'];
            }

            $customerId = $this->customerSession->getCustomerId();
            $agentDataTable = $this->objectManager->create(
                'Webkul\MagentoChatSystem\Model\ResourceModel\AgentData\Collection'
            )->getTable('chatsystem_agentdata');

            $customerData = $this->objectManager->create(
                'Webkul\MagentoChatSystem\Model\CustomerData'
            )->getCollection()
            ->addFieldToFilter('customer_id', ['eq' => $customerId]);

            $customerUniqueId = $customerData->getFirstItem()->getUniqueId();

            $chatHistoryCollection = $this->objectManager->create(
                'Webkul\MagentoChatSystem\Model\Message'
            )->getCollection()
            ->addFieldToFilter(
                ['sender_unique_id', 'receiver_unique_id'],
                [['eq' => $customerUniqueId], ['eq' => $customerUniqueId]]
            )->setOrder('date', 'DESC');

            if ($msgDate) {
                $msgDate = date_create($msgDate);
                $msgDate = date_format($msgDate, 'Y-m-d H:i:s');
                $chatHistoryCollection->addFieldToFilter('date', ['gteq'=> $msgDate]);
            }

            $chatHistoryCollection->getSelect()->join(
                $agentDataTable.' as adt',
                'main_table.sender_unique_id = adt.agent_unique_id OR 
                main_table.receiver_unique_id = adt.agent_unique_id',
                ['agent_name' => 'agent_name']
            );

            if ($agentName !== '') {
                $chatHistoryCollection->getSelect()->where(
                    'adt.agent_name like "%'.$agentName.'%"'
                );
            }
            $this->history = $chatHistoryCollection;
        }
        return $this->history;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getHistoryCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'chat.history.list.pager'
            )->setCollection(
                $this->getHistoryCollection()
            );
            $this->setChild('pager', $pager);
            $this->getHistoryCollection()->load();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getCustomerUniqueId()
    {
        $customerId = $this->customerSession->getCustomerId();
        $customerData = $this->objectManager->create(
            'Webkul\MagentoChatSystem\Model\CustomerData'
        )->getCollection()
        ->addFieldToFilter('customer_id', ['eq' => $customerId]);

        return $customerData->getFirstItem()->getUniqueId();
    }

    /**
     * Retrieve information from carrier configuration.
     *
     * @param string $field
     *
     * @return void|false|string
     */
    public function getConfigData($field)
    {
        $path = 'chatsystem/chat_options/'.$field;
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );
    }

    /**
     * return message type
     *
     * @param string $message
     * @return string
     */
    public function getMessageType($message)
    {
        $type = 'text';
        $file = $this->urlDecoder->decode($message);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $filesystem = $objectManager->get(\Magento\Framework\Filesystem::class);
        $directory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);

        $fileName = 'chatsystem/attachments/'.ltrim($file, '/');

        $filePath = $directory->getAbsolutePath($fileName);
        
        if ($directory->isFile($fileName)) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'gif':
                    $contentType = 'image/gif';
                    $type = 'image';
                    break;
                case 'jpg':
                    $contentType = 'image/jpeg';
                    $type = 'image';
                    break;
                case 'jpeg':
                    $contentType = 'image/jpeg';
                    $type = 'image';
                    break;
                case 'PNG':
                    $contentType = 'image/png';
                    $type = 'image';
                    break;
                case 'png':
                    $contentType = 'image/png';
                    $type = 'image';
                    break;
                default:
                    $contentType = 'application/octet-stream';
                    $type = 'file';
                    break;
            }
        }
        return $type;
    }
}
