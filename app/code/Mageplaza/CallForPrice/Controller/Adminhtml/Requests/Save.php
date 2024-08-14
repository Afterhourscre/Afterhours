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
 * Class Save
 * @package Mageplaza\CallForPrice\Controller\Adminhtml\Requests
 */
class Save extends Requests
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $data           = $this->getRequest()->getPost('request');

        if (!$data) {
            return $resultRedirect->setPath('mpcallforprice/*/');
        }

        /** @var \Mageplaza\CallForPrice\Model\Requests $request */
        $request = $this->_initRequest();

        if (!$this->isRequestExist($request)) {
            $this->messageManager->addErrorMessage(__('This $request does not exist.'));

            return $resultRedirect->setPath('mpcallforprice/*/');
        }

        if (!empty($data)) {
            $this->prepareData($request, $data);
            $this->_getSession()->setData('callforprice_request_data', $data);
        }

        try {
            $request->save();
            $this->_getSession()->setData('callforprice_request_data', false);

            $this->messageManager->addSuccessMessage(__('You saved the request.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $redirectBack = true;
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $redirectBack = true;
            $this->messageManager->addErrorMessage(__('We cannot save the $request.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        return ($redirectBack)
            ? $resultRedirect->setPath('mpcallforprice/*/edit', ['request_id' => $request->getRequestId()])
            : $resultRedirect->setPath('mpcallforprice/*/');
    }

    /**
     * @param \Mageplaza\CallForPrice\Model\Requests $model
     *
     * @return bool
     */
    protected function isRequestExist(\Mageplaza\CallForPrice\Model\Requests $model)
    {
        $requestId = $this->getRequest()->getParam('request_id');

        return (!$model->getRequestId() && $requestId) ? false : true;
    }

    /**
     * @param       $request
     * @param array $data
     *
     * @return $this
     */
    protected function prepareData($request, $data = [])
    {
        /** convert store data*/
        $storeData         = implode(',', $data['store_ids']);
        $data['store_ids'] = $storeData;

        $request->addData($data);

        return $this;
    }
}