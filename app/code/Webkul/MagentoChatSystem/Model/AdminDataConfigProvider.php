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
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\Url as CustomerUrlManager;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class AdminDataConfigProvider
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
     * View file system
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $viewFileSystem;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        FormKey $formKey,
        ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        \Webkul\MagentoChatSystem\Helper\Data $helper,
        \Magento\Framework\View\Asset\Repository $viewFileSystem,
        \Magento\Backend\Helper\Data $adminHelper,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->authSession = $authSession;
        $this->formKey = $formKey;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
        $this->adminHelper = $adminHelper;
        $this->viewFileSystem = $viewFileSystem;
        $this->objectManager = $objectManager;
        $this->aclRetriever = $aclRetriever;
    }
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $defaultImageUrl = $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ).
        'chatsystem/admin/default.png';

        $output['formKey'] = $this->formKey->getFormKey();
        $output['adminData'] = $this->getAdminData();
        $output['adminChatName'] = $this->helper->getConfigData('chat_config', 'chat_name');
        $output['isAdminLoggedIn'] = $this->isAdminLoggedIn();
        $output['isSuperAdmin'] = $this->isSuperAdmin();
        $output['isServerRunning'] = $this->isServerRunning();
        $output['host'] = $this->helper->getConfigData('chat_config', 'host_name');
        $output['port'] = $this->helper->getConfigData('chat_config', 'port_number');
        $output['adminBaseUrl'] = $this->adminHelper->getUrl('chatsystem/message/save');
        $output['adminUpdateChatUrl'] = $this->adminHelper->getUrl('chatsystem/chat/updatestatus');
        $output['removeAssignedChatUrl'] = $this->adminHelper->getUrl('chatsystem/chat/removesuperadmin');
        $output['AdminloadMsgUrl'] = $this->adminHelper->getUrl('chatsystem/message/loadhistory');
        $output['AdminclearMsgUrl'] = $this->adminHelper->getUrl('chatsystem/message/clearhistory');

        if ($this->helper->getConfigData('chat_config', 'admin_image')) {
            $output['adminImage'] = $this ->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ).
            'chatsystem/admin/'.
            $this->helper->getConfigData('chat_config', 'admin_image');
        } else {
            $output['adminImage'] = $defaultImageUrl;
        }
        
        $output['defaultImageUrl'] = $defaultImageUrl;

        return $output;
    }

    /**
     * Retrieve customer data
     *
     * @return array
     */
    private function getAdminData()
    {
        $adminData = [];
        $id = $this->authSession->getUser()->getId();
        if ($this->isAdminLoggedIn()) {
            $adminData['name'] = $this->authSession->getUser()->getName();
            $adminData['email'] = $this->authSession->getUser()->getEmail();
            $adminData['id'] = $this->authSession->getUser()->getId();

            $agentModelCollection = $this->objectManager->create(
                'Webkul\MagentoChatSystem\Model\AgentData'
            )->getCollection()
            ->addFieldToFilter('agent_id', ['eq' => $this->authSession->getUser()->getId()]);

            $agentStatus = $agentModelCollection->getFirstItem()->getChatStatus();
            $adminData['status'] = $agentStatus;
            $adminData['agent_unique_id'] = $agentModelCollection->getFirstItem()->getAgentUniqueId();
        }
        return $adminData;
    }

    protected function isSuperAdmin()
    {
        $id = $this->authSession->getUser()->getId();
        if ($this->isAdminLoggedIn()) {
            $userRole = $this->authSession->getUser()->getRole();
            $resources = $this->aclRetriever->getAllowedResourcesByRole($userRole->getId());
            if ($userRole->getRoleName() == 'Administrators' &&
                in_array('Magento_Backend::all', $resources)
            ) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function isServerRunning()
    {
        $host = $this->helper->getConfigData('chat_config', 'host_name');
        $port = $this->helper->getConfigData('chat_config', 'port_number');
        $chkServerRunning = exec('timeout 1s telnet '.$host.' '.$port.'');
        $getBrack = explode(' ', $chkServerRunning);

        if ((count($getBrack) > 2) && (strtolower($getBrack[0]) == 'escape')) {
            return true;
        } else {
            return false;
        }
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
