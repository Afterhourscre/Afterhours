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
use Magento\Security\Model\AdminSessionsManager;

/**
 * User backend observer model for passwords
 */
class AgentLogoutObserver implements ObserverInterface
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

    protected $adminSessionInfo;

    /**
     * @param \Magento\User\Model\Backend\Config\ObserverConfig $observerConfig
     * @param \Magento\User\Model\ResourceModel\User $userResource
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\User\Model\Backend\Config\ObserverConfig $observerConfig,
        AdminSessionsManager $sessionsManager,
        \Magento\User\Model\ResourceModel\User $userResource,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Security\Model\AdminSessionInfoFactory $adminSessionInfo,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->userResource = $userResource;
        $this->aclRetriever = $aclRetriever;
        $this->objectManager = $objectManager;
        $this->authSession = $authSession;
        $this->sessionsManager = $sessionsManager;
        $this->observerConfig = $observerConfig;
        $this->adminSessionInfo = $adminSessionInfo;
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
        $user = $this->sessionsManager->getCurrentSession();
        $isSessionExpired = $this->adminSessionInfo->create()->isSessionExpired();
        
        if ($isSessionExpired) {
            if ($user->getUserId()) {
                $agentModel = $this->objectManager->create(
                    'Webkul\MagentoChatSystem\Model\AgentData'
                );

                $agentModelCollection = $this->objectManager->create(
                    'Webkul\MagentoChatSystem\Model\AgentData'
                )->getCollection()
                ->addFieldToFilter('agent_id', ['eq' => $user->getUserId()]);
                if ($agentModelCollection->getSize()) {
                    $entityId = $agentModelCollection->getFirstItem()->getEntityId();
                    $model = $agentModel->load($entityId);
                    $model->setChatStatus(0);
                    $model->setId($entityId)->save();
                }
            }
        }
    }
}
