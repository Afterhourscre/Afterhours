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
    
namespace Webkul\MagentoChatSystem\Controller\Adminhtml\Agent\View;

class AssignedChat extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Webkul\MagentoChatSystem\Model\AgentDataFactory $agentFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->agentFactory = $agentFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_MagentoChatSystem::assigned_view');
    }

    public function execute()
    {
        $entityId = (int)$this->getRequest()->getParam('entity_id');
        $model = $this->agentFactory->create()->load($entityId);
        $this->_coreRegistry->register('agent_data', $model);
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
