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
 * @package     Mageplaza_Barclaycard
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barclaycard\Controller\Adminhtml\Credential;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\Barclaycard\Gateway\Config\Direct;
use Mageplaza\Barclaycard\Helper\Request;
use Mageplaza\Barclaycard\Helper\Response;

/**
 * Class Test
 * @package Mageplaza\Barclaycard\Controller\Adminhtml\Credential
 */
class Test extends Action
{
    /**
     * @var Direct
     */
    private $config;

    /**
     * @var Response
     */
    private $responseHelper;

    /**
     * @var Request
     */
    private $requestHelper;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * Test constructor.
     *
     * @param Context $context
     * @param Direct $config
     * @param Request $requestHelper
     * @param Response $responseHelper
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Direct $config,
        Request $requestHelper,
        Response $responseHelper,
        JsonFactory $resultJsonFactory
    ) {
        $this->config            = $config;
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
        $request = $this->getRequest();
        $storeId = $request->getParam('websiteId');

        $password = $request->getParam('password');
        if ($password === '******') {
            $password = $this->config->getPassword($storeId);
        }

        $shaIn = $request->getParam('sha_in');
        if ($shaIn === '******') {
            $shaIn = $this->config->getShaIn($storeId);
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $url  = $this->requestHelper->getApiUrl(Request::DIRECT);
        $body = [
            'PSPID'     => $request->getParam('psp_id'),
            'ORDERID'   => date('dmy-Gis') . '-TEST',
            'USERID'    => $request->getParam('user_id'),
            'PSWD'      => $password,
            'AMOUNT'    => 100,
            'CURRENCY'  => 'GBP',
            'CARDNO'    => '4000000000000002',
            'ED'        => '10' . (substr(date('Y'), 2) + 3),
            'CVC'       => 123,
            'OPERATION' => 'PAU',
        ];

        $this->requestHelper->appendShaSign($body, $shaIn, $request->getParam('hash_algorithm'));

        $response = $this->requestHelper->sendRequest($url, [], $body);

        if ($error = $this->responseHelper->hasError($response)) {
            return $resultJson->setData(['type' => 'error', 'message' => __('Invalid credentials') . ' - ' . $error]);
        }

        return $resultJson->setData(['type' => 'success', 'message' => __('Credentials are valid')]);
    }
}
