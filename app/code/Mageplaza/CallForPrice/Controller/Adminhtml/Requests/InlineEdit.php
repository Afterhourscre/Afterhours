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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\CallForPrice\Model\RequestsFactory;

/**
 * Class InlineEdit
 * @package Mageplaza\CallForPrice\Controller\Adminhtml\Requests
 */
class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    public $jsonFactory;

    /**
     * @var RequestsFactory
     */
    public $requestFactory;

    /**
     * InlineEdit constructor.
     *
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param RequestsFactory $requestFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        RequestsFactory $requestFactory
    )
    {
        $this->jsonFactory    = $jsonFactory;
        $this->requestFactory = $requestFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson   = $this->jsonFactory->create();
        $error        = false;
        $messages     = [];
        $requestItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && !empty($requestItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error'    => true,
            ]);
        }

        $key       = array_keys($requestItems);
        $requestId = !empty($key) ? (int)$key[0] : '';
        /** @var \Mageplaza\CallForPrice\Model\Requests $requests */
        $request = $this->requestFactory->create()->load($requestId);
        try {
            $requestData = $requestItems[$requestId];
            $request->addData($requestData)->save();
        } catch (LocalizedException $e) {
            $messages[] = $this->getErrorWithRequestId($request, $e->getMessage());
            $error      = true;
        } catch (\RuntimeException $e) {
            $messages[] = $this->getErrorWithRequestId($request, $e->getMessage());
            $error      = true;
        } catch (\Exception $e) {
            $messages[] = $this->getErrorWithRequestId($request, __('Something went wrong while saving the Request.'));
            $error      = true;
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error'    => $error
        ]);
    }

    /**
     * Add Request id to error message
     *
     * @param \Mageplaza\CallForPrice\Model\Requests $requests
     * @param string $errorText
     *
     * @return string
     */
    public function getErrorWithRequestId(\Mageplaza\CallForPrice\Model\Requests $requests, $errorText)
    {
        return '[Request ID: ' . $requests->getRequestId() . '] ' . $errorText;
    }
}
