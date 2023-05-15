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
 * @package     Mageplaza_Barclaycard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barclaycard\Helper;

use Mageplaza\Barclaycard\Model\Source\Environment;
use Zend_Http_Client;
use Zend_Http_Response;

/**
 * Class Request
 * @package Mageplaza\Barclaycard\Helper
 */
class Request extends Data
{
    const TEST_URL = 'https://mdepayments.epdq.co.uk/ncol/test';
    const LIVE_URL = 'https://payments.epdq.co.uk/ncol/prod';

    const HOSTED = '/orderstandard_utf8.asp';
    const DIRECT = '/orderdirect.asp';
    const MAINT  = '/maintenancedirect.asp';

    /**
     * @param string $path
     *
     * @return string
     */
    public function getApiUrl($path)
    {
        $url = $this->getEnvironment() === Environment::SANDBOX ? self::TEST_URL : self::LIVE_URL;

        return $url . $path;
    }

    /**
     * @param string $url
     * @param array $headers
     * @param array $body
     * @param array $config
     * @param string $method
     *
     * @return array
     */
    public function sendRequest($url, $headers = [], $body = [], $config = [], $method = Zend_Http_Client::POST)
    {
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        $curl = $this->curlFactory->create();
        $curl->setConfig(array_merge(['timeout' => 120, 'verifyhost' => 2], $config));
        $curl->write($method, $url, Zend_Http_Client::HTTP_1, $headers, $this->toString($body));

        $response = $curl->read();

        $curl->close();

        return $this->toArray(Zend_Http_Response::extractBody($response));
    }

    /**
     * @param string $response
     *
     * @return array
     */
    private function toArray($response)
    {
        $xml = simplexml_load_string($response);

        return self::jsonDecode(self::jsonEncode($xml));
    }

    /**
     * @param array $txnArray
     *
     * @return string
     */
    private function toString($txnArray)
    {
        $result = '';
        foreach ($txnArray as $field => $value) {
            if ($value === null) {
                continue;
            }

            if ($result) {
                $result .= '&';
            }

            $result .= $field . '=' . $value;
        }

        return $result;
    }
}
