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

use Extait\Cookie\Api\Data\CategoryInterface;
use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Controller\Adminhtml\AbstractController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractController
{
    /**
     * Save the cookie category entity.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $categoryDetails = $this->getRequest()->getParam('category_details');
        $storeID = $this->getRequest()->getParam('store', 0);

        try {
            /** @var \Extait\Cookie\Model\Category $category */
            $category = $this->categoryFactory->create();

            if (isset($categoryDetails[CategoryInterface::ID])) {
                $this->categoryResourceModel->load($category, $categoryDetails[CategoryInterface::ID]);
            }

            $category->setData('store_id', $storeID);
            $category->setName($categoryDetails[CategoryInterface::NAME]);
            $category->setDescription($categoryDetails[CategoryInterface::DESCRIPTION]);
            $this->categoryResourceModel->save($category);

            $cookieCollection = $this->cookieCollectionFactory->create();
            $cookieCollection->addFieldToFilter(CookieInterface::CATEGORY_ID, ['eq' => $category->getId()]);
            $currentCookieIds = $cookieCollection->getAllIds();

            // If there are cookies ids, set the category ID to Cookie.
            if (!empty($categoryDetails['cookies_ids'])) {
                foreach (array_diff($currentCookieIds, $categoryDetails['cookies_ids']) as $cookiesID) {
                    /** @var \Extait\Cookie\Model\Cookie $cookie */
                    $cookie = $this->cookieFactory->create();
                    $this->cookieResourceModel->load($cookie, $cookiesID);
                    $cookie->setCategoryId(null);
                    $cookie->setIsSystem(null);
                    $this->cookieResourceModel->save($cookie);
                }
                foreach ($categoryDetails['cookies_ids'] as $cookiesID) {
                    /** @var \Extait\Cookie\Model\Cookie $cookie */
                    $cookie = $this->cookieFactory->create();
                    $this->cookieResourceModel->load($cookie, $cookiesID);

                    $cookie->setCategoryId($category->getId());
                    $cookie->setIsSystem($category->getIsSystem());

                    $this->cookieResourceModel->save($cookie);
                }
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__('An error occurred: %1', $e->getMessage()));
        }

        if (isset($categoryDetails[CategoryInterface::ID])) {
            $this->messageManager->addSuccessMessage('Category has been updated.');
        } else {
            $this->messageManager->addSuccessMessage('Category has been created.');
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }
}
