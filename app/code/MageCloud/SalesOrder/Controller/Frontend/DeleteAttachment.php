<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 31.10.18
 * Time: 10:08
 */

namespace MageCloud\SalesOrder\Controller\Frontend;

use Magento\Framework\Json\Helper\Data as JsonHelper;
use MageCloud\SalesOrder\Model\SalesOrderFactory;

class DeleteAttachment extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $_storeManager;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_file;

    /**
     * @var SalesOrderFactory
     */
    protected $salesOrderFactory;

    /**
     * DeleteAttachment constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param JsonHelper $jsonHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param SalesOrderFactory $salesOrderFactory
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        JsonHelper $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        SalesOrderFactory $salesOrderFactory,
        \Magento\Framework\Filesystem\Driver\File $file
    ) {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_storeManager = $storeManager;
        $this->salesOrderFactory = $salesOrderFactory;
        $this->_file = $file;
    }


    public function execute(){

        $_postData = $this->getRequest()->getPost();

        $message = "";
        $newFileName = "";
        $success = false;

        $orderId = $_postData->get('order_id');
        $orderItemId = $_postData->get('order_item_id');
        $mediaRootDir = $this->_mediaDirectory->getAbsolutePath();
        $_postDataFileName = explode( 'media/', $_postData['filename']);
        $_fileName = $mediaRootDir.(isset($_postDataFileName[1]) ? $_postDataFileName[1] : $_postDataFileName[0]);

        $id = $_postData->get('id');
        if ($this->_file->isExists($_fileName))  {
            try{

//                $_mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $this->_file->deleteFile($_fileName);
//                var_dump(count(glob($_mediaUrl.'salesOrderAttach/'.$orderId.'/'.$orderItemId.'/*')));
//                if (count(glob($_mediaUrl.'salesOrderAttach/'.$orderId.'/'.$orderItemId.'/*'))) {
//                    rmdir(explode('salesOrderAttach/', $_fileName));
//                    rmdir($_mediaUrl.'salesOrderAttach/'.$orderId);
//                    rmdir($_mediaUrl.'salesOrderAttach/');
//                }

                $message = "File removed successfully.";
                $success = true;
            } catch (Exception $ex) {
                $message = $ex->getMessage();
                $success = false;
            }
        }else{
            $message = "File not found.";
            $success = false;
        }

        $model = $this->salesOrderFactory->create();
        $model->load($id);
        $model->delete();

        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
            'message' => $message,
            'data' => '',
            'success' => $success
        ]);
    }
}