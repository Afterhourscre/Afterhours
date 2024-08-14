<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Controller\Adminhtml\Label;

use Aheadworks\OnSale\Api\LabelRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Aheadworks\OnSale\Controller\Adminhtml\Label
 */
class Edit extends Action
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
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param LabelRepositoryInterface $labelRepository;
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        LabelRepositoryInterface $labelRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->labelRepository = $labelRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $labelId = (int) $this->getRequest()->getParam('id');
        if ($labelId) {
            try {
                $label = $this->labelRepository->get($labelId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This label no longer exists.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_OnSale::labels')
            ->getConfig()->getTitle()->prepend(
                $labelId
                    ? __('Edit "%1" label', $label->getName())
                    : __('New Label')
            );
        return $resultPage;
    }
}
