<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Controller\Adminhtml\Requests;

use Mageplaza\CallForPrice\Controller\Adminhtml\Requests;

/**
 * Class Index
 * @package Mageplaza\CallForPrice\Controller\Adminhtml\Requests
 */
class Index extends Requests
{
    /**
     * execute the action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
       
        $resultPage->setActiveMenu('Mageplaza_CallForPrice::Requests');
         // die('321');
        $resultPage->getConfig()->getTitle()->prepend((__('Requests')));

        return $resultPage;
    }
}
