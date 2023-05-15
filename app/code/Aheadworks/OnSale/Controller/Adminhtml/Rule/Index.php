<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Rule;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action as BackendAction;
use Aheadworks\OnSale\Model\Rule\ReindexNotice;

/**
 * Class Index
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Rule
 */
class Index extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_OnSale::rules';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ReindexNotice
     */
    private $reindexNotice;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ReindexNotice $reindexNotice
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ReindexNotice $reindexNotice
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->reindexNotice = $reindexNotice;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        if ($this->reindexNotice->isEnabled()) {
            $this->messageManager->addNoticeMessage($this->reindexNotice->getText());
        }

        $resultPage->setActiveMenu('Aheadworks_OnSale::rules');
        $resultPage->getConfig()->getTitle()->prepend(__('Rules'));
        return $resultPage;
    }
}
