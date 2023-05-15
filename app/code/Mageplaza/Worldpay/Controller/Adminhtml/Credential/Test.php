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
 * @package     Mageplaza_Worldpay
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Worldpay\Controller\Adminhtml\Credential;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\Worldpay\Helper\Request;
use Mageplaza\Worldpay\Helper\Response;
use Worldpay\Worldpay;
use Worldpay\WorldpayException;

/**
 * Class Test
 * @package Mageplaza\Worldpay\Controller\Adminhtml\Credential
 */
class Test extends Action
{
    /**
     * @var Request
     */
    private $requestHelper;

    /**
     * @var Response
     */
    private $responseHelper;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Test constructor.
     *
     * @param Context $context
     * @param Request $requestHelper
     * @param Response $responseHelper
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Request $requestHelper,
        Response $responseHelper,
        JsonFactory $resultJsonFactory
    ) {
        $this->requestHelper     = $requestHelper;
        $this->responseHelper    = $responseHelper;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        if (!class_exists(Worldpay::class)) {
            $result = ['type' => 'error', 'message' => __('Worldpay library is not installed correctly')];

            return $resultJson->setData($result);
        }

        $request    = $this->getRequest();
        $serviceKey = $request->getParam('service_key');
        $clientKey  = $request->getParam('client_key');

        if ($serviceKey === '******') {
            $serviceKey = $this->requestHelper->getServiceKey();
        }

        if ($clientKey === '******') {
            $clientKey = $this->requestHelper->getClientKey();
        }

        $tokenReq = [
            'reusable'      => false,
            'paymentMethod' => [
                'name'        => 'test card holder',
                'expiryMonth' => 2,
                'expiryYear'  => '20' . (substr(date('Y'), 2) + 3),
                'cardNumber'  => '5454545454545454',
                'type'        => 'Card',
                'cvc'         => '123'
            ],
            'clientKey'     => $clientKey
        ];

        $this->requestHelper->setServiceKey($serviceKey);
        $tokenRes = $this->requestHelper->sendRequest(Request::TOKEN_URL, $tokenReq);

        if ($error = $this->responseHelper->hasError($tokenRes)) {
            return $resultJson->setData(['type' => 'error', 'message' => $error]);
        }

        $orderReq = [
            'token'               => $this->responseHelper->getInfo($tokenRes, 'token'),
            'orderType'           => 'ECOM',
            'amount'              => 500,
            'currencyCode'        => 'GBP',
            'name'                => 'test name', // optional
            'orderDescription'    => 'Order description',
            'customerOrderCode'   => 'Order code',
            'settlementCurrency'  => 'GBP',
            'shopperLanguageCode' => 'en', // optional
            'is3DSOrder'          => false,
            'billingAddress'      => [
                'address1'    => 'address1',
                'postalCode'  => 'postCode',
                'city'        => 'city',
                'countryCode' => 'GB',
            ],
            'deliveryAddress'     => [
                'address1'    => 'address1',
                'postalCode'  => 'postCode',
                'city'        => 'city',
                'countryCode' => 'GB',
            ],
            'shopperEmailAddress' => 'test@order.com', // optional
            'shopperIpAddress'    => $this->requestHelper->getIpAddress(),
            'shopperSessionId'    => '123', // test
            'shopperUserAgent'    => $this->requestHelper->getUserAgent(),
            'shopperAcceptHeader' => '*/*',
        ];

        try {
            $worldpay = new Worldpay($serviceKey);
            $worldpay->createOrder($orderReq);

            return $resultJson->setData(['type' => 'success', 'message' => __('Credentials are valid')]);
        } catch (WorldpayException $e) {
            return $resultJson->setData(['type' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
