<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Controller\Adminhtml\Category;

use Aheadworks\Faq\Model\Category;
use Aheadworks\Faq\Api\CategoryRepositoryInterface as CategoryRepository;
use Aheadworks\Faq\Api\Data\CategoryInterfaceFactory as CategoryFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Backend\Model\View\Result\Page;

/**
 * FAQ Category Edit
 */
class Edit extends AbstractAction
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param CategoryRepository $categoryRepository
     * @param CategoryFactory $categoryFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        CategoryRepository $categoryRepository,
        CategoryFactory $categoryFactory,
        PageFactory $resultPageFactory
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return Page
     */
    private function initAction()
    {
        /**
         * @var Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_Faq::category')
            ->addBreadcrumb(__('FAQ'), __('FAQ'))
            ->addBreadcrumb(__('Manage Categories'), __('Manage Categories'));
        return $resultPage;
    }

    /**
     * Edit Category page
     *
     * @return Page|Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('category_id');
        /** @var Category $model */
        $model = $this->categoryFactory->create();

        if ($id) {
            $model = $this->categoryRepository->getById($id);
            if (!$model->getCategoryId()) {
                $this->messageManager->addErrorMessage(__('This page no longer exists.'));
                /**
                 * Redirect $resultRedirect
                 */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        /**
         * @var Page $resultPage
         */
        $resultPage = $this->initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Category') : __('New Category'),
            $id ? __('Edit Category') : __('New Category')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getCategoryId() ? $model->getName() : __('New Category'));

        return $resultPage;
    }
}
