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

namespace Extait\Cookie\Controller\Adminhtml\Cookie;

use Extait\Cookie\Api\Data\CookieInterface;
use Extait\Cookie\Controller\Adminhtml\AbstractController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractController
{
    /**
     * Save the cookie entity.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $cookieDetails = $this->getRequest()->getParam('cookie_details');
        $storeID = $this->getRequest()->getParam('store', 0);

        try {
            /** @var \Extait\Cookie\Model\Cookie $cookie */
            $cookie = $this->cookieFactory->create();
            $category = $this->categoryRepository->get($cookieDetails[CookieInterface::CATEGORY_ID]);

            if (isset($cookieDetails[CookieInterface::ID])) {
                $this->cookieResourceModel->load($cookie, $cookieDetails[CookieInterface::ID]);
            } else {
                $cookieCollection = $this->cookieCollectionFactory->create();
                $cookieCollection->addFieldToFilter(CookieInterface::NAME, ['eq' => $cookieDetails[CookieInterface::NAME]]);
                if ($cookieCollection->count()) {
                    $this->messageManager->addErrorMessage(__('Cookie "%1" already exist', $cookieDetails[CookieInterface::NAME]));
                    /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setPath('*/*/new');

                    return $resultRedirect;
                }
            }

            $cookie->setName($cookieDetails[CookieInterface::NAME]);
            $cookie->setDescription($cookieDetails[CookieInterface::DESCRIPTION]);
            $cookie->setCategoryId($cookieDetails[CookieInterface::CATEGORY_ID]);
            $cookie->setIsSystem($category->getIsSystem());
            $cookie->setData('store_id', $storeID);

            $this->cookieResourceModel->save($cookie);
        } catch (\Exception $e) {
            throw new LocalizedException(__('An error occurred: %1', $e->getMessage()));
        }

        if (isset($cookieDetails[CookieInterface::ID])) {
            $this->messageManager->addSuccessMessage('Cookie has been updated.');
        } else {
            $this->messageManager->addSuccessMessage('Cookie has been created.');
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }
}
