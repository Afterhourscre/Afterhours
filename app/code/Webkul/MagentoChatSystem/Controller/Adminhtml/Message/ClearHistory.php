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
namespace Webkul\MagentoChatSystem\Controller\Adminhtml\Message;

use Webkul\MagentoChatSystem\Api\CustomerDataRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Webkul\MagentoChatSystem\Model\ResourceModel\Message\CollectionFactory;

class ClearHistory extends \Magento\Backend\App\Action
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
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * View file system
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_viewFileSystem;

    /**
     * @param \Magento\Backend\App\Action\Context              $context
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory       $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime      $date
     * @param \Magento\Framework\View\Asset\Repository         $viewFileSystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\View\Asset\Repository $viewFileSystem,
        CustomerDataRepositoryInterface $customerDataRepository,
        CustomerFactory $customerFactory,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->_viewFileSystem = $viewFileSystem;
        $this->_date = $date;
        $this->customerDataRepository = $customerDataRepository;
        $this->customerFactory = $customerFactory;
        $this->collectionFactory = $collectionFactory;
    }
   
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);
        $data = $this->getRequest()->getParam('formData');
        if (isset($data['customerId'])) {
            $customer = $this->customerFactory->create()->load($data['customerId']);
            $chatCustomerModel = $this->customerDataRepository->getByCustomerId($data['customerId']);
            $customerUniqueId = $chatCustomerModel->getFirstItem()->getUniqueId();

            $messageModel = $this->collectionFactory
                ->create()
                ->addFieldToFilter(
                    ['sender_unique_id', 'receiver_unique_id'],
                    [['eq' => $customerUniqueId], ['eq' => $customerUniqueId]]
                );
            if ($messageModel->getSize()) {
                foreach ($messageModel as $value) {
                    $value->delete();
                }
                $response->setMessage(__('Chat History Deleted.'));
            } else {
                $response->setError(true);
                $response->setMessage(__('Chat history not available.'));
            }
            return $this->resultJsonFactory->create()->setJsonData($response->toJson());
        }
    }
}
