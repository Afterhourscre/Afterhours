<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Label;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Aheadworks\OnSale\Model\Label\Image\Uploader;
use Magento\Framework\Controller\Result\Json;

/**
 * Class UploadImage
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Label
 */
class UploadImage extends Action
{
    /**
     * @var string
     */
    const FILE_ID = 'img_file';

    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_OnSale::labels';

    /**
     * @var Uploader
     */
    private $imageUploader;

    /**
     * @param Context $context
     * @param Uploader $imageUploader
     */
    public function __construct(
        Context $context,
        Uploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Image upload action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $result = $this->imageUploader->uploadToMediaFolder(self::FILE_ID);
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }

        return $resultJson->setData($result);
    }
}
