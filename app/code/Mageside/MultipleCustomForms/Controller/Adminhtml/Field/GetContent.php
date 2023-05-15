<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Controller\Adminhtml\Field;

class GetContent extends \Magento\Downloadable\Controller\Adminhtml\Downloadable\Product\Edit\Link
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageside_MultipleCustomForms::mageside_multiple_custom_forms';

    /**
     * @var \Mageside\MultipleCustomForms\Model\FileUploader
     */
    protected $_fileUploader;

    /**
     * GetContent constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Mageside\MultipleCustomForms\Model\FileUploader $fileUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mageside\MultipleCustomForms\Model\FileUploader $fileUploader
    ) {
        $this->_fileUploader = $fileUploader;
        parent::__construct($context, $productBuilder, $resultPageFactory);
    }

    public function execute()
    {
        $resource = $this->getRequest()->getParam('file');
        $basePath = $this->_fileUploader->getBasePath();
        $filePath = $this->_fileUploader->getFilePath($basePath, $resource);
        $this->_processDownload($filePath, 'file');
    }
}
