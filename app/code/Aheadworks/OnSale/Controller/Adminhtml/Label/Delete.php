<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Label;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\OnSale\Api\LabelRepositoryInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Label
 */
class Delete extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_OnSale::labels';

    /**
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @param Context $context
     * @param LabelRepositoryInterface $labelRepository
     */
    public function __construct(
        Context $context,
        LabelRepositoryInterface $labelRepository
    ) {
        parent::__construct($context);
        $this->labelRepository = $labelRepository;
    }

    /**
     * Delete label action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $labelId = (int)$this->getRequest()->getParam('id');
        if ($labelId) {
            try {
                $this->labelRepository->deleteById($labelId);
                $this->messageManager->addSuccessMessage(__('You deleted the label.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Something went wrong while deleting the label.'));
        return $resultRedirect->setPath('*/*/');
    }
}
