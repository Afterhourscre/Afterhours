<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_MagentoChatSystem
 * @author Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
namespace Webkul\MagentoChatSystem\Model\Plugin;

use Magento\Backend\Model\Auth\Session;
use Magento\Security\Model\AdminSessionsManager as SessionsManager;

/**
 * Magento\Backend\Model\Auth\Session decorator
 */
class AdminSessionsManager
{

    /**
     * @var AdminSessionsManager
     */
    private $sessionsManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Admin Session prolong functionality
     *
     * @param Session $session
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundProcessLogout(SessionsManager $session, \Closure $proceed)
    {
        $user = $session->getCurrentSession();
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
        $this->removeAssignedAgent($user);

        $result = $proceed();
        return $result;
    }

    protected function removeAssignedAgent($user)
    {
        $assignedAgent = $this->objectManager->create(
            'Webkul\MagentoChatSystem\Model\AssignedChat'
        )->getCollection()
        ->addFieldToFilter('agent_id', ['eq' => $user->getUserId()])
        ->addFieldToFilter('chat_status', ['eq' => 1]);

        if ($assignedAgent->getSize()) {
            $agentId = $assignedAgent->getFirstItem()->getAgentId();

            foreach ($assignedAgent as $assigned) {
                $assigned->setChatStatus(0);
                $assigned->setId($assigned->getEntityId());
                $assigned->save();
            }

            $totalAssignedChat = $this->objectManager->create(
                'Webkul\MagentoChatSystem\Model\TotalAssignedChat'
            )->getCollection()
            ->addFieldToFilter('agent_id', ['eq' => $agentId]);
            $totalAssignId = $totalAssignedChat->getFirstItem()->getEntityId();
            $totalActiveChat = $totalAssignedChat->getFirstItem()->getTotalActiveChat();
            $totalAssignedModel = $this->objectManager->create(
                'Webkul\MagentoChatSystem\Model\TotalAssignedChat'
            )->load($totalAssignId);
            $totalAssignedModel->setTotalActiveChat($totalActiveChat-1);
            $totalAssignedModel->setId($totalAssignId)->save();
        }
    }
}
