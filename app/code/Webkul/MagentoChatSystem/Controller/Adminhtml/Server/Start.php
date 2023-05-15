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

class Start extends \Magento\Backend\App\Action
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
        $this->authSession = $authSession;
        $this->layoutFactory = $layoutFactory;
        $this->directoryList = $directoryList;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);
        if (!$this->isServerRunning()) {
            $response->setRoot($this->directoryList->getRoot());
            $rootPath = $this->directoryList->getRoot();
            $node = exec('whereis node');
            $nodePath = explode(' ', $node);
            if (!isset($nodePath[1])) {
                $node = exec('whereis nodejs');
                $nodePath = explode(' ', $node);
            }
            if (isset($nodePath[1])) {
                if (substr(php_uname(), 0, 7) == "Windows") {
                    pclose(popen("start /B ". $nodePath[1].' '.$rootPath.'/app.js', "r"));
                } else {
                    exec($nodePath[1].' '.$rootPath.'/app.js' . " > /dev/null &");
                }
                $response->setMessage(
                    __('Server Running.')
                );
            } else {
                $response->setError(true);
                $response->setMessage(
                    __('Node path can not be found, make sure Node is installed on this server.')
                );
            }
        } elseif (!$response->getError()) {
            $response->setMessage(
                __('Node server already running.')
            );
        }

        $agentId = $this->authSession->getUser()->getId();
        $agentDataModel = $this->_objectManager->create('Webkul\MagentoChatSystem\Model\AgentData')
            ->getCollection();
        $entityId = 0;
        if ($agentDataModel->getSize()) {
            $entityId = $agentDataModel->getFirstItem()->getEntityId();
        }
        if ($entityId) {
            $agentDataModel = $this->_objectManager->create(
                'Webkul\MagentoChatSystem\Model\AgentData'
            )->load($entityId);
            $agentDataModel->setAgentId($agentId);
            // $agentDataModel->setChatStatus(1);
            $agentDataModel->setId($entityId);
            $agentDataModel->save();
        } else {
            $user = $this->authSession->getUser();
            $agentDataModel = $this->_objectManager->create(
                'Webkul\MagentoChatSystem\Model\AgentData'
            )
            ->setAgentId($agentId)
            ->setAgentUniqueId($this->generateUniqueId())
            // ->setChatStatus(1)
            ->setAgentEmail($user->getEmail())
            ->setAgentName($user->getFirstName(). ' '.$user->getLastName())
            ->save();
        }
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }

    /**
     * check if the node server is already running on a specific port.
     *
     * @return bool
     */
    public function isServerRunning()
    {
        $data = $this->getRequest()->getParams();
        
        $chkServerRunning = exec(
            'timeout 2s telnet '.$this->getRequest()->getParam('hostname').' '.
            $this->getRequest()->getParam('port').''
        );
        $getBrack = explode(' ', $chkServerRunning);

        if ((count($getBrack) > 2) && (strtolower($getBrack[0]) == 'escape')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * generate
     *
     * @return void
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
        return $id;
    }
}
