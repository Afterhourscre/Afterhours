<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse;

use Aheadworks\Helpdesk\Api\QuickResponseRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Aheadworks\Helpdesk\Controller\Adminhtml\QuickResponse
 */
class Edit extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk::quick_responses';

    /**
     * @var QuickResponseRepositoryInterface
     */
    private $quickResponseRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param QuickResponseRepositoryInterface $quickResponseRepository;
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        QuickResponseRepositoryInterface $quickResponseRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->quickResponseRepository = $quickResponseRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $quickResponseId = (int) $this->getRequest()->getParam('id');
        if ($quickResponseId) {
            try {
                $quickResponse = $this->quickResponseRepository->get($quickResponseId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This quick response no longer exists.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_Helpdesk::quick_responses')
            ->getConfig()->getTitle()->prepend(
                $quickResponseId
                    ? __('Edit "%1" quick response', $quickResponse->getTitle())
                    : __('New quick response')
            );
        return $resultPage;
    }
}
