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
namespace Webkul\MagentoChatSystem\Helper;

use Magento\Security\Model\ConfigInterface;
use Webkul\MagentoChatSystem\Model\ResourceModel\AgentData as AgentResource;
use Webkul\MagentoChatSystem\Api\AgentDataRepositoryInterface;
use Webkul\MagentoChatSystem\Api\Data\AgentDataInterfaceFactory;

/**
 * MpVendorAttributeManager data helper.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * Core store config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @param Magento\Framework\App\Helper\Context        $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param Magento\Store\Model\StoreManagerInterface   $storeManager
     * @param \Magento\Customer\Model\Session             $customerSession
     * @param \Magento\Eav\Model\Entity $eavEntity
     * @param \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributeCollection
     * @param \Magento\Framework\View\Element\Html\Date $dateElement
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Webkul\MagentoChatSystem\Api\Data\AgentRatingInterfaceFactory $agentRatingFactory,
        \Magento\Security\Model\ResourceModel\AdminSessionInfo\CollectionFactory $adminSessionInfoCollectionFactory,
        ConfigInterface $securityConfig,
        AgentResource $resource,
        AgentDataRepositoryInterface $agentRepository,
        AgentDataInterfaceFactory $agentDataFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_request = $request;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->agentRatingFactory = $agentRatingFactory;
        $this->adminSessionInfoCollectionFactory = $adminSessionInfoCollectionFactory;
        $this->securityConfig = $securityConfig;
        $this->resource = $resource;
        $this->agentRepository = $agentRepository;
        $this->date = $date;
    }

    /**
     * Retrieve information from carrier configuration.
     *
     * @param string $field
     *
     * @return void|false|string
     */
    public function getConfigData($group, $field)
    {
        $path = 'chatsystem/'.$group.'/'.$field;

        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
    }

    public function sendNewCustomerEmail(
        $customer,
        $sender,
        $templateParams = [],
        $storeId = null
    ) {
        $customerViewHelper = $this->_objectManager('Magento\Customer\Helper\View');
        $storeId = $this->storeManager->getStore()->getId();
        $email = $customer->getEmail();
        $transport = $this->transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($templateParams)
            ->setFrom($this->_scopeConfig->getValue($sender, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId))
            ->addTo($email, $customerViewHelper->getCustomerName($customer))
            ->getTransport();

        $transport->sendMessage();
    }

    
    /**
     * get total rating stars
     *
     * @return array
     */
    public function getAgentRating($agentId = null)
    {
        if (!$agentId) {
            $agentId = $this->getAgentId();
        }
        $ratingsTotal = [];
        $totalRatingFor1 = $this->agentRatingFactory->create()->getCollection()
            ->addFieldToFilter('agent_id', ['eq' => $agentId])
            ->addFieldToFilter('rating', ['eq' => 1])
            ->addFieldToFilter('status', ['eq' => 1])
            ->getSize();
        $totalRatingFor2 = $this->agentRatingFactory->create()->getCollection()
            ->addFieldToFilter('agent_id', ['eq' => $agentId])
            ->addFieldToFilter('rating', ['eq' => 2])
            ->addFieldToFilter('status', ['eq' => 1])
            ->getSize();
        $totalRatingFor3 = $this->agentRatingFactory->create()->getCollection()
            ->addFieldToFilter('agent_id', ['eq' => $agentId])
            ->addFieldToFilter('rating', ['eq' => 3])
            ->addFieldToFilter('status', ['eq' => 1])
            ->getSize();
        $totalRatingFor4 = $this->agentRatingFactory->create()->getCollection()
            ->addFieldToFilter('agent_id', ['eq' => $agentId])
            ->addFieldToFilter('rating', ['eq' => 4])
            ->addFieldToFilter('status', ['eq' => 1])
            ->getSize();
        $totalRatingFor5 = $this->agentRatingFactory->create()->getCollection()
            ->addFieldToFilter('agent_id', ['eq' => $agentId])
            ->addFieldToFilter('rating', ['eq' => 5])
            ->addFieldToFilter('status', ['eq' => 1])
            ->getSize();
        return $ratingsTotal = [
            '1' => $totalRatingFor1,
            '2' => $totalRatingFor2,
            '3' => $totalRatingFor3,
            '4' => $totalRatingFor4,
            '5' => $totalRatingFor5,
        ];
    }

    /**
     * get admin users data.
     *
     * @return array
     */
    public function getAgentCurrentStatus($agentId)
    {
        if (!$agentId) {
            return false;
        }
        $ids = [$agentId];
        $status = false;
        $connection = $this->resource->getConnection();
        $gmtTimestamp = $this->date->gmtTimestamp();
        $sessionLifeTime = $this->securityConfig->getAdminSessionLifetime();
        $sessionCollection = $this->createAdminSessionInfoCollection();
        $sessionCollection2 = clone $sessionCollection;
        $sessionCollection2
            ->addFieldToFilter('user_id', ['in' => $ids])
            ->addFieldToFilter(
                'updated_at',
                ['gt' => $connection->formatDate($gmtTimestamp - $sessionLifeTime)]
            );
        $sessionCollection->getSelect()->order('user_id DESC')->distinct(true)->group('user_id');
        $sessionCollection2->getSelect()->order('user_id DESC')->distinct(true)->group('user_id');
        
        $loggedInIds = [];
        foreach ($sessionCollection2 as $sessionModel) {
            $loggedInIds[] = $sessionModel->getUserId();
        }
        
        foreach ($sessionCollection as $model) {
            if (!in_array($model->getUserId(), $loggedInIds) &&
                $model->getStatus() == 1 &&
                $model->getUserId() == $agentId
            ) {
                $model->setStatus(0)->save();
            } elseif (in_array($model->getUserId(), $loggedInIds)) {
                $status = $model->getStatus();
            }
        }
        $agent = $this->agentRepository->getByAgentId($agentId);
        $agent->setChatStatus($status);
        $this->agentRepository->save($agent);

        return $status;
    }

    /**
     * get agent id
     *
     * @return int
     */
    private function getAgentId()
    {
        $id = 0;
        $adminUserCollection = $this->_objectManager->create('Webkul\MagentoChatSystem\Model\AssignedChat')
            ->getCollection()
            ->addFieldToFilter('customer_id', ['eq' => $this->_customerSession->getCustomerId()]);
        
        if ($adminUserCollection->getSize()) {
            return $adminUserCollection->getFirstItem()->getAgentId();
        }
        return $id;
    }

    /**
     * @return \Magento\Security\Model\ResourceModel\AdminSessionInfo\Collection
     */
    protected function createAdminSessionInfoCollection()
    {
        return $this->adminSessionInfoCollectionFactory->create();
    }
}
