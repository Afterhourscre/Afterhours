<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Aheadworks\Helpdesk\Api\QuickResponseRepositoryInterface;

/**
 * Class Delete
 * @package Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse
 */
class Delete extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk::quick_responses';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var QuickResponseRepositoryInterface
     */
    protected $quickResponseRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param QuickResponseRepositoryInterface $quickResponseRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        QuickResponseRepositoryInterface $quickResponseRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->quickResponseRepository = $quickResponseRepository;
    }

    /**
     * Delete quick response action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $quickResponseId = (int) $this->getRequest()->getParam('id');
        if ($quickResponseId) {
            try {
                $this->quickResponseRepository->deleteById($quickResponseId);
                $this->messageManager->addSuccessMessage(__('Quick response has been successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Something went wrong while deleting quick response'));
        return $resultRedirect->setPath('*/*/');
    }
}
