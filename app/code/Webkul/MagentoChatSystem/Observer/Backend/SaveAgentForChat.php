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
class SaveAgentForChat implements ObserverInterface
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
        $user = $observer->getEvent()->getObject();

        if ($user->getId()) {
            /* @var $userRole \Magento\Authorization\Model\Role */
            $userRole = $user->getRole();
            $userRule = $user->getRules();
            $resources = $this->aclRetriever->getAllowedResourcesByRole($userRole->getId());
            if ($userRole->getRoleName() == 'ChatSystem' ||
                $userRole->getRoleName() == 'Administrators' ||
                $userRole->getRoleName() == 'ChatManager' ||
                in_array('Magento_Backend::all', $resources) ||
                in_array('Webkul_MagentoChatSystem::chatsystem', $resources)
            ) {
                $agentType = $this->getAgentType($userRole);
                
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
                    $model->setChatStatus($user->getIsActive());
                    $model->setId($entityId)->save();
                } else {
                    $agentModel->setAgentId($user->getId());
                    $agentModel->setAgentUniqueId($this->generateUniqueId());
                    $agentModel->setAgentEmail($user->getEmail());
                    $agentModel->setAgentName($user->getFirstName(). ' '.$user->getLastName());
                    $agentModel->setChatStatus($user->getIsActive());
                    $agentModel->setAgentType($agentType);
                    $agentModel->save();
                }
            }
        }
    }
    /**
     * Get agent type
     * 0 = admin, 1 = chatmanager, 2 = chatAgent
     *
     * @param \Magento\Authorization\Model\Role $userRole
     * @return int
     */
    private function getAgentType($userRole)
    {
        $agentType = 0;
        if ($userRole->getRoleName() == 'ChatManager') {
            $agentType = 1;
        } elseif ($userRole->getRoleName() == 'ChatSystem') {
            $agentType = 2;
        }

        return $agentType;
    }

    /**
     * generate unique id for agent.
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
        return implode($pass);
    }
}
