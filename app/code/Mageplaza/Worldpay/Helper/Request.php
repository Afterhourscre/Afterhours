<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Worldpay\Helper;

use Zend_Http_Client;
use Zend_Http_Response;

/**
 * Class Request
 * @package Mageplaza\Worldpay\Helper
 */
class Request extends Data
{
    const TOKEN_URL   = 'https://api.worldpay.com/v1/tokens';
    const ORDER_URL   = 'https://api.worldpay.com/v1/orders';
    const CAPTURE_URL = 'https://api.worldpay.com/v1/orders/%txn_id%/capture';
    const REFUND_URL  = 'https://api.worldpay.com/v1/orders/%txn_id%/refund';
    const CANCEL_URL  = 'https://api.worldpay.com/v1/orders/%txn_id%'; // delete request
    const CONFIRM_URL = 'https://api.worldpay.com/v1/orders/%txn_id%'; // put request
    const CVC_URL     = 'https://api.worldpay.com/v1/tokens/%token%'; // put request

    /**
     * @param string $url
     * @param array $txnArray
     * @param string $method
     *
     * @return array|bool
     */
    public function sendRequest($url, $txnArray, $method = Zend_Http_Client::POST)
    {
        $headers = ['Content-type: application/json', 'Authorization: ' . $this->getServiceKey()];

        $curl = $this->curlFactory->create();

        $curl->setConfig(['timeout' => 120, 'verifyhost' => 2]);
        $curl->write($method, $url, '1.1', $headers, self::jsonEncode($txnArray));

        $response = $curl->read();

        $curl->close();

        return $response === '' ? false : self::jsonDecode(Zend_Http_Response::extractBody($response));
    }

    /**
     * @param array $txnArray
     *
     * @return array
     */
    public function createTransaction(&$txnArray)
    {
        $url = $txnArray['url'];

        unset($txnArray['url']);

        return $this->sendRequest($url, $txnArray);
    }
}
