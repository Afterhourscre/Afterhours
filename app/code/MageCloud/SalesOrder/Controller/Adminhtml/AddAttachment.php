<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 31.10.18
 * Time: 10:07
 */

namespace MageCloud\SalesOrder\Controller\Adminhtml;

use Magento\Framework\App\Action\Action;
use MageCloud\SalesOrder\Model\SalesOrderFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class AddAttachment extends Action {

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
     * @var SalesOrderFactory
     */
    protected $salesOrderFactory;

    /**
     * AddAttachment constructor.
     * @param Context $context
     * @param JsonHelper $jsonHelper
     * @param JsonFactory $resultJsonFactory
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     * @param SalesOrderFactory $salesOrderFactory
     * @param StoreManagerInterface $storeManager
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Context $context,
        JsonHelper $jsonHelper,
        JsonFactory $resultJsonFactory,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory,
        SalesOrderFactory $salesOrderFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->salesOrderFactory = $salesOrderFactory;
        $this->_storeManager = $storeManager;
    }


    public function execute(){

        $_postData = $this->getRequest()->getPost();

        $message = "";
        $newFileName = "";
        $error = false;
        $data = array();
        $orderId = $_postData->get('order_id');
        $orderItemId = $_postData->get('order_item_id');
        $pathFirstImage = $_postData->get('image_path');
        if ($pathFirstImage) {
            $imageHash = explode('/', $pathFirstImage);
            $unicPath = 'salesOrderAttach/'.$imageHash[6].'/'.$orderItemId.'/';
        } else {
            $unicPath = 'salesOrderAttach/'.$orderId.'-'.uniqid().'/'.$orderItemId.'/';
        }

        try{
            $target = $this->_mediaDirectory->getAbsolutePath($unicPath);

            //attachment is the input file name posted from your form
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'image_name']);

            $_fileType = $uploader->getFileExtension();
//            $newFileName = uniqid().'.'.$_fileType;
//            $newFileName = $uploader->getUploadedFileName().'.'.$_fileType;

            /** Allowed extension types */
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'ai', 'eps', 'psd']);
            /** rename file name if already exists */
            $uploader->setAllowRenameFiles(true);

//            $result = $uploader->save($target, $newFileName); //Use this if you want to change your file name
            $result = $uploader->save($target);
            if ($result['file']) {

                $_mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $_iconArray = array(
                    'pdf' => $_mediaUrl.'salesOrderAttach/default/icon-pdf.png',
                    'doc' => $_mediaUrl.'salesOrderAttach/default/icon-doc.png',
                    'docx' => $_mediaUrl.'salesOrderAttach/default/icon-docx.png',
                    'xls' => $_mediaUrl.'salesOrderAttach/default/icon-xls.png',
                    'xlsx' => $_mediaUrl.'salesOrderAttach/default/icon-xlsx.png',
                    'csv' => $_mediaUrl.'salesOrderAttach/default/icon-csv.png',
                );

                if(isset($_iconArray[$_fileType])){
                    $_src = $_iconArray[$_fileType];
                }else{
                    $newFileName = $uploader->getUploadedFileName();
                    $_src = $_mediaUrl.$unicPath.$newFileName;
                }


                $params = [
                    'post_data' => $_postData,
                    'file_name' => $newFileName,
                    'image_src' => $_src
                ];

                $id = null;
                try {
                    $id = $this->update($params);
                } catch (\Exception $e) {

                }

                $error = false;
                $message = "File has been successfully uploaded";
                $unicId = uniqid();
                $html = '<div class="image item base-image" data-role="image" id="'.$id.'">
                            <a href="'.$_mediaUrl.$unicPath.$newFileName.'" download>
                                <div class="product-image-wrapper">
                                    <img class="product-image" data-role="image-element" src="'.$_src.'" alt="">
                                </div>
                            </a>
                            <div class="actions">
                                <button type="button" class="action-remove" data-role="delete-button" data-image="'.$unicPath.$newFileName.'" title="Delete image"><span>Delete</span></button>
                            </div>
                        </div>';

                $data = array('image_name' => $newFileName, 'image_path' => $_src, 'fileType' => $_fileType, 'html' => $html, 'unicId' => $unicId);
            }
        } catch (\Exception $e) {
            $error = true;
            $message = $e->getMessage();
        }

        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
            'message' => $message,
            'data' => $data,
            'error' => $error
        ]);
    }

    /**
     * @param $data
     * @return mixed|null
     * @throws \Exception
     */
    protected function update($data)
    {
        if (empty($data)) {
            return null;
        }

        $postData = isset($data['post_data']) ? $data['post_data'] : [];
        $fileName = isset($data['file_name']) ? $data['file_name'] : '';
        $src = isset($data['image_src']) ? $data['image_src'] : '';

        $model = $this->salesOrderFactory->create();
        $model->setOrderId(isset($postData['order_id']) ? $postData['order_id'] : '');
        $model->setOrderItemId(isset($postData['order_item_id']) ? $postData['order_item_id'] : '');
        $model->setImageName($fileName);
        $model->setImagePath($src);
        $model->save();

        return $model->getId();

    }
}