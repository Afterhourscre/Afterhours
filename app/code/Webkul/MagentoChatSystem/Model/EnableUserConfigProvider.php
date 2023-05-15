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

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Webkul\MagentoChatSystem\Api\CustomerDataRepositoryInterface;
use Webkul\MagentoChatSystem\Api\Data\CustomerDataInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Webkul\MagentoChatSystem\Model\ResourceModel\AgentData\Collection as AgentCollection;
use Magento\Customer\Model\ResourceModel\Online\Grid\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class EnableUserConfigProvider
{

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var CustomerSession
     */
    private $authSession;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var UrlInterface
     */
    protected $helper;

    /**
     * @var CustomerDataRepository
     */
    private $customerDataRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Authorization\Model\Acl\AclRetriever
     */
    protected $aclRetriever;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @param \Magento\Backend\Model\Auth\Session        $authSession
     * @param FormKey                                    $formKey
     * @param ScopeConfigInterface                       $scopeConfig
     * @param CustomerDataRepository                     $customerDataRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param UrlInterface                               $urlBuilder
     * @param \Webkul\MagentoChatSystem\Helper\Data      $helper
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        FormKey $formKey,
        ScopeConfigInterface $scopeConfig,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        CustomerDataRepositoryInterface $customerDataRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        CustomerRepository $customerRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        \Webkul\MagentoChatSystem\Helper\Data $helper,
        \Magento\Framework\View\Asset\Repository $viewFileSystem,
        CollectionFactory $onlineCustomerCollectionFactory
    ) {
        $this->authSession = $authSession;
        $this->formKey = $formKey;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->aclRetriever = $aclRetriever;
        $this->helper = $helper;
        $this->customerDataRepository = $customerDataRepository;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->objectManager = $objectManager;
        $this->viewFileSystem = $viewFileSystem;
        $this->onlineCustomerCollectionFactory = $onlineCustomerCollectionFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $output['formKey'] = $this->formKey->getFormKey();
        $output['enableUserData'] = $this->getEnableUsers();
        $output['isAdminLoggedIn'] = $this->isAdminLoggedIn();
        $output['host'] = $this->helper->getConfigData('config', 'host_name');
        $output['port'] = $this->helper->getConfigData('config', 'port_number');

        return $output;
    }

    /**
     * Retrieve customer data
     *
     * @return array
     */
    private function getEnableUsers()
    {
        $usersData = [];
        $id = $this->authSession->getUser()->getId();
        if ($this->isAdminLoggedIn()) {
            $userRole = $this->authSession->getUser()->getRole();
            $resources = $this->aclRetriever->getAllowedResourcesByRole($userRole->getId());
            $agentDataCollection = $this->objectManager->create(
                'Webkul\MagentoChatSystem\Model\AssignedChat'
            )->getCollection()
            ->addFieldToFilter('agent_id', ['eq' => $id])
            // ->addFieldToFilter('chat_status', ['eq' => 1])
            ->addFieldToSelect('customer_id');
            
            $customerIds = [];
            $customerIds = $agentDataCollection->getData();
            $enabledUserFilter[] = $this->filterBuilder
                ->setField(CustomerDataInterface::CUSTOMER_ID)
                ->setConditionType('in')
                ->setValue($agentDataCollection->getData())
                ->create();

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilters($enabledUserFilter)
                ->create();
            
            $users = $this->customerDataRepository->getList($searchCriteria)->getItems();
            foreach ($users as $user) {
                $customer = $this->customerRepository->getById($user['customer_id']);
                $onlineCollection = $this->onlineCustomerCollectionFactory->create()
                    ->addFieldToFilter('customer_id', ['eq' => $customer->getId()]);
                
                $user['chat_status'] = $onlineCollection->getSize()? 1: 0;
                if ($user['chat_status'] == 1 && $onlineCollection->getSize()) {
                    $statusClass = 'active';
                    $status = 1;
                } elseif ($user['chat_status'] == 2 && $onlineCollection->getSize()) {
                    $statusClass = 'busy';
                    $status = 2;
                } elseif ($user['chat_status'] == 0) {
                    $statusClass = 'offline';
                    $status = 0;
                }
                
                $customerData = $this->customerDataRepository->getByCustomerId($customer->getId());
                $customerData->setChatStatus($status);
                $this->customerDataRepository->save($customerData);
                $defaultImageUrl = $this->viewFileSystem->getUrlWithParams('Webkul_MagentoChatSystem::images/default.png', []);
                $userImage = '';
                if (isset($user['image']) && $user['image'] != '') {
                    $userImage = $user['image'];
                    $image = $this ->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).
                    'chatsystem/profile/'
                    .$user['customer_id'].'/'.$userImage;
                } else {
                    $image  = $defaultImageUrl;
                }
                
                $usersData[] = [
                    'customerId' => $user['customer_id'],
                    'uniqueId'   => $user['unique_id'],
                    'customerName'          => $customer->getFirstname().' '.$customer->getLastname(),
                    'email'     => $customer->getEmail(),
                    'chat_status'   => $user['chat_status'],
                    'class' => $statusClass,
                    'image' => $image
                ];
            }
        }
        return $usersData;
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     * @codeCoverageIgnore
     */
    private function isAdminLoggedIn()
    {
        return (bool)$this->authSession->isLoggedIn();
    }
}
