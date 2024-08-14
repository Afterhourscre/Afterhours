<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\MultipleCustomForms\Controller\Adminhtml\Form\Submission;

use Magento\Backend\App\Action;

class View extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageside_MultipleCustomForms::mageside_multiple_custom_forms';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);

        $store = $this->storeManager->getStore(0);
        $this->storeManager->setCurrentStore($store->getCode());
    }

    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create('page');
        $resultPage->getConfig()->getTitle()->prepend('Submission');

        return $resultPage;
    }
}
