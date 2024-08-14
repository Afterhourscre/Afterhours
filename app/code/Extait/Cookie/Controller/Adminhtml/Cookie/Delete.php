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

class Delete extends AbstractController
{
    /**
     * Delete cookie action.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $cookieID = $this->getRequest()->getParam(CookieInterface::ID);

        try {
            if (isset($cookieID)) {
                /** @var \Extait\Cookie\Model\Cookie $cookie */
                $cookie = $this->cookieFactory->create();

                $this->cookieResourceModel->load($cookie, $cookieID);
                $this->cookieResourceModel->delete($cookie);
            } else {
                throw new LocalizedException(__('There is no Cookie with ID %1', $cookieID));
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__('An error occurred: %1', $e->getMessage()));
        }

        $this->messageManager->addSuccessMessage('Cookie has been deleted.');

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        return $resultRedirect;
    }
}
