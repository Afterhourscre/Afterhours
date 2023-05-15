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
namespace Webkul\MagentoChatSystem\Controller\Profile;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Model\Session as CustomerSession;

class Upload extends \Magento\Customer\Controller\AbstractAccount
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

    /**
     * @var CustomerSession
     */
    private $customerSession;

    private $_storeManager;

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
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        CustomerSession $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);
        $path = $this->_filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            'chatsystem/profile/'.$this->customerSession->getCustomerId()
        );
        $url = $this ->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).
                'chatsystem/profile/'.$this->customerSession->getCustomerId();
        try {
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'file']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $uploader->setAllowCreateFolders(true);
            $result = $uploader->save($path);
            $collection = $this->_objectManager->create('Webkul\MagentoChatSystem\Model\CustomerData')
                ->getCollection()
                ->addFieldToFilter('customer_id', ['eq' => $this->customerSession->getCustomerId()]);
            if ($collection->getSize()) {
                $entityId = $collection->getFirstItem()->getEntityId();
                $model = $this->_objectManager->create('Webkul\MagentoChatSystem\Model\CustomerData')->load($entityId);
                $model->setImage($result['file']);
                $model->setId($entityId);
                $model->save();
            }
            $response->setImageName($url.'/'.$result['file']);
            $response->setMessage(
                __('Image updated successfully.')
            );
        } catch (\Exception $e) {
            $response->setMessage(
                $e->getMessage()
            );
            $response->setError(true);
        }
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}
