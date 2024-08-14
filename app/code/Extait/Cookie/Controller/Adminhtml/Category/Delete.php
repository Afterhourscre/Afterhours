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

use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Controller\Adminhtml\AbstractController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Delete extends AbstractController
{
    /**
     * Delete category action.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $categoryID = $this->getRequest()->getParam(CookieInterface::ID);

        try {
            if (isset($categoryID)) {
                /** @var \Extait\Cookie\Model\Category $category */
                $category = $this->categoryFactory->create();

                $this->categoryResourceModel->load($category, $categoryID);
                $this->categoryResourceModel->delete($category);
            } else {
                throw new LocalizedException(__('There is no Category with ID %1', $categoryID));
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__('An error occurred: %1', $e->getMessage()));
        }

        $this->messageManager->addSuccessMessage('Category has been deleted.');

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }
}
