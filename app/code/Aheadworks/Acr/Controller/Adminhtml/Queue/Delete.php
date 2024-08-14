<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Acr\Controller\Adminhtml\Queue;

use Aheadworks\Acr\Api\QueueRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Delete
 * @package Aheadworks\Acr\Controller\Adminhtml\Queue
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::mail_log';

    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * @param Context $context
     * @param QueueRepositoryInterface $queueRepository
     */
    public function __construct(
        Context $context,
        QueueRepositoryInterface $queueRepository
    ) {
        parent::__construct($context);
        $this->queueRepository = $queueRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $queueId = (int)$this->getRequest()->getParam('id');
        if ($queueId) {
            try {
                $this->queueRepository->deleteById($queueId);
                $this->messageManager->addSuccessMessage(__('Email was successfully deleted.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the email.'));
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
