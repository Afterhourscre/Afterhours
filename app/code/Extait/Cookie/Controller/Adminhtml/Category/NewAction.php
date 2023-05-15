<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Cookie
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

namespace Extait\Cookie\Controller\Adminhtml\Category;

use Extait\Cookie\Controller\Adminhtml\AbstractController;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends AbstractController
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($this->cookieHelper->isModuleEnabled() === false) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->_backendUrl->getUrl());

            return $resultRedirect;
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Extait_Cookie::category');
        $resultPage->getConfig()->getTitle()->prepend((__('New Category')));

        return $resultPage;
    }
}
