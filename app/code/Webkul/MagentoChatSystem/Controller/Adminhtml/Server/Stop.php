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
namespace Webkul\MagentoChatSystem\Controller\Adminhtml\Server;

class Stop extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;
     /**
      * @var string
      */
    protected $_customerEntityTypeId;

    protected $directoryList;

    /**
     * @var CustomerSession
     */
    private $authSession;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Cache\FrontendInterface $attributeLabelCache
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->authSession = $authSession;
        $this->directoryList = $directoryList;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);
        $data = $this->getRequest()->getParams();
        $response->setRoot($this->directoryList->getRoot());
        $rootPath = $this->directoryList->getRoot();
        $getUserPath = exec('whereis fuser');
        if ($getUserPath) {
            $getUserPath = explode(' ', $getUserPath);
            if (isset($getUserPath[1])) {
                $stopServer = exec($getUserPath[1].' -k '.$data['port'].'/tcp');
            }
            $agentId = $this->authSession->getUser()->getId();
            $agentDataModel = $this->_objectManager->create('Webkul\MagentoChatSystem\Model\AgentData')
                ->getCollection()
                ->addFieldToFilter('agent_id', ['eq' => $agentId]);
            $entityId = 0;
            if ($agentDataModel->getSize()) {
                $entityId = $agentDataModel->getFirstItem()->getEntityId();
            }
            if ($entityId) {
                $agentDataModel = $this->_objectManager->create(
                    'Webkul\MagentoChatSystem\Model\AgentData'
                )->load($entityId);
                $agentDataModel->setChatStatus(1);
                $agentDataModel->setId($entityId);
                $agentDataModel->save();
            }
        }
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }

    function isServerRunning($host, $port = 80, $timeout = 6)
    {
        $fp = @fsockopen($host, $port, $errno, $errstr, 2);
        if (!$fp) {
            return false;
        } else {
            return true;
        }
    }
}
