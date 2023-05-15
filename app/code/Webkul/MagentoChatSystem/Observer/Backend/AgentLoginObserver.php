<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_MagentoChatSystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\MagentoChatSystem\Observer\Backend;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * User backend observer model for passwords
 */
class AgentLoginObserver implements ObserverInterface
{
    /**
     * Backend configuration interface
     *
     * @var \Magento\User\Model\Backend\Config\ObserverConfig
     */
    protected $observerConfig;

    /**
     * Admin user resource model
     *
     * @var \Magento\User\Model\ResourceModel\User
     */
    protected $userResource;

    /**
     * Backend authorization session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    protected $aclRetriever;

    /**
     * @param \Magento\User\Model\Backend\Config\ObserverConfig $observerConfig
     * @param \Magento\User\Model\ResourceModel\User $userResource
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\User\Model\Backend\Config\ObserverConfig $observerConfig,
        \Magento\User\Model\ResourceModel\User $userResource,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->userResource = $userResource;
        $this->aclRetriever = $aclRetriever;
        $this->objectManager = $objectManager;
        $this->authSession = $authSession;
        $this->observerConfig = $observerConfig;
    }

    /**
     * Save current admin password to prevent its usage when changed in the future.
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        /* @var $user \Magento\User\Model\User */
        $user = $observer->getEvent()->getUser();

        if ($user->getId()) {
            $userRole = $user->getRole();
            $userRule = $user->getRules();
            $resources = $this->aclRetriever->getAllowedResourcesByRole($userRole->getId());
            if ($userRole->getRoleName() == 'ChatSystem' ||
                $userRole->getRoleName() == 'Administrators' ||
                $userRole->getRoleName() == 'ChatManager' ||
                in_array('Magento_Backend::all', $resources) ||
                in_array('Webkul_MagentoChatSystem::chatsystem', $resources)
            ) {
                $agentType = 0; // 0 for Admin
                $chatStatus = 1;
                if ($userRole->getRoleName() == 'ChatSystem') {
                    $agentType = 2; //2 for agent
                    $chatStatus = $user->getIsActive();
                }
                if ($userRole->getRoleName() == 'ChatManager') {
                    $agentType = 1; //1 for agent manager
                    $chatStatus = $user->getIsActive();
                }
                $agentModel = $this->objectManager->create(
                    'Webkul\MagentoChatSystem\Model\AgentData'
                );

                $agentModelCollection = $this->objectManager->create(
                    'Webkul\MagentoChatSystem\Model\AgentData'
                )->getCollection()
                ->addFieldToFilter('agent_id', ['eq' => $user->getId()]);
                
                if ($agentModelCollection->getSize()) {
                    $entityId = $agentModelCollection->getFirstItem()->getEntityId();
                    $model = $agentModel->load($entityId);
                    $model->setChatStatus($chatStatus);
                    $model->setId($entityId)->save();
                } else {
                    $agentModel->setAgentId($user->getId());
                    $agentModel->setAgentUniqueId($this->generateUniqueId());
                    $agentModel->setAgentEmail($user->getEmail());
                    $agentModel->setAgentName($user->getFirstName(). ' '.$user->getLastName());
                    $agentModel->setChatStatus($chatStatus);
                    $agentModel->setAgentType($agentType);
                    $agentModel->save();
                }
            }
        }
    }

    public function generateUniqueId()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
}
