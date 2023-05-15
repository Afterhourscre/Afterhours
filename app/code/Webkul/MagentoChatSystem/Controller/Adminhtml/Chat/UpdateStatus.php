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
namespace Webkul\MagentoChatSystem\Controller\Adminhtml\Chat;

class UpdateStatus extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @param \Magento\Backend\App\Action\Context              $context
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory       $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime      $date
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->date = $date;
    }
   
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);
        $data = $this->getRequest()->getParam('formData');
        if (isset($data['adminId']) && $data['adminId'] !== '') {
            $chatCustomer = $this->_objectManager->create('Webkul\MagentoChatSystem\Model\AgentData')
                ->getCollection()
                ->addFieldToFilter('agent_id', ['eq' => $data['adminId']]);
            $entityId = $chatCustomer->getFirstItem()->getEntityId();
            $agentModel = $this->_objectManager->create(
                'Webkul\MagentoChatSystem\Model\AgentData'
            )->load($entityId);
            $agentModel->setChatStatus($data['status']);
            $agentModel->setId($entityId);
            $agentModel->save();

            /*unassign all chats*/
            if ($data['status'] == 0) {
                $assignedChat = $this->_objectManager->create('Webkul\MagentoChatSystem\Model\AssignedChat')
                    ->getCollection()
                    ->addFieldToFilter('agent_id', ['eq' => $data['adminId']])
                    ->addFieldToFilter('chat_status', ['eq' => 1]);

                if ($assignedChat->getSize()) {
                    foreach ($assignedChat as $assigned) {
                        $assigned->setChatStatus(0);
                        $assigned->setId($assigned->getEntityId())->save();
                    }
                }
            }
            $response->setMessage(
                __('Status Updated.')
            );
        } else {
            $response->setError(true);
        }
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}
