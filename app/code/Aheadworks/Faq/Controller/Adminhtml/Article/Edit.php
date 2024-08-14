<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\Faq\Controller\Adminhtml\Article;

use Aheadworks\Faq\Model\Article;
use Aheadworks\Faq\Api\ArticleRepositoryInterface as ArticleRepository;
use Aheadworks\Faq\Api\Data\ArticleInterfaceFactory as ArticleFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Backend\Model\View\Result\Page;

/**
 * FAQ Article Edit
 */
class Edit extends AbstractAction
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var ArticleFactory
     */
    private $articleFactory;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param ArticleRepository $articleRepository
     * @param ArticleFactory $articleFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ArticleRepository $articleRepository,
        ArticleFactory $articleFactory,
        PageFactory $resultPageFactory
    ) {
        $this->articleRepository = $articleRepository;
        $this->articleFactory = $articleFactory;
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
        $resultPage->setActiveMenu('Aheadworks_Faq::article')
            ->addBreadcrumb(__('FAQ'), __('FAQ'))
            ->addBreadcrumb(__('Manage Articles'), __('Manage Articles'));
        return $resultPage;
    }

    /**
     * Edit Article page
     *
     * @return Page|Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('article_id');
        /** @var Article $model */
        $model = $this->articleFactory->create();

        if ($id) {
            $model = $this->articleRepository->getById($id);
            if (!$model->getArticleId()) {
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
            $id ? __('Edit Article') : __('New Article'),
            $id ? __('Edit Article') : __('New Article')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Articles'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getArticleId() ? $model->getTitle() : __('New Article'));

        return $resultPage;
    }
}
