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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Worldpay\Test\Unit\Gateway\Request;

use Magento\Checkout\Model\Session;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Mageplaza\Worldpay\Gateway\Config\Cards;
use Mageplaza\Worldpay\Gateway\Request\AuthorizationRequest;
use Mageplaza\Worldpay\Helper\Request;
use Mageplaza\Worldpay\Helper\Response;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class AuthorizationRequestTest
 * @package Mageplaza\Worldpay\Test\Unit\Gateway\Request
 */
class AuthorizationRequestTest extends TestCase
{
    /**
     * @var Request|PHPUnit_Framework_MockObject_MockObject
     */
    private $helper;

    /**
     * @var Response|PHPUnit_Framework_MockObject_MockObject
     */
    private $responseHelper;

    /**
     * @var Session|PHPUnit_Framework_MockObject_MockObject
     */
    private $session;

    /**
     * @var Cards|PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var AuthorizationRequest
     */
    private $object;

    protected function setUp()
    {
        $this->helper         = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $this->responseHelper = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $this->session        = $this->getMockBuilder(Session::class)->disableOriginalConstructor()->getMock();
        $this->config         = $this->getMockBuilder(Cards::class)->disableOriginalConstructor()->getMock();

        $this->object = new AuthorizationRequest($this->helper, $this->responseHelper, $this->session, $this->config);
    }

    public function testBuild()
    {
        $paymentDataObject = $this->getMockBuilder(PaymentDataObjectInterface::class)->getMock();

        $payment = $this->getMockBuilder(Payment::class)->disableOriginalConstructor()->getMock();
        $paymentDataObject->method('getPayment')->willReturn($payment);

        $order = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $payment->method('getOrder')->willReturn($order);

        $info = [
            'cc_holder'    => 'test card holder',
            'cc_exp_month' => 10,
            'cc_exp_year'  => 22,
            'cc_number'    => 4242,
            'cc_cid'       => 123
        ];
        $payment->method('getAdditionalInformation')->willReturn($info);

        $incrId       = 12;
        $currencyCode = 'GBP';
        /** @var OrderAddressInterface|PHPUnit_Framework_MockObject_MockObject $billing */
        $billing = $this->getMockBuilder(OrderAddressInterface::class)->getMock();
        /** @var OrderAddressInterface|PHPUnit_Framework_MockObject_MockObject $shipping */
        $shipping = $this->getMockBuilder(OrderAddressInterface::class)->getMock();
        $order->method('getIncrementId')->willReturn($incrId);
        $order->method('getOrderCurrencyCode')->willReturn($currencyCode);
        $order->method('getBillingAddress')->willReturn($billing);
        $order->method('getShippingAddress')->willReturn($shipping);

        $buildSubject = [
            'payment' => $paymentDataObject,
            'amount'  => 100
        ];

        $this->helper->method('convertAmount')->willReturn($buildSubject['amount']);

        $email     = 'email';
        $firstName = 'first name';
        $lastName  = 'last name';

        $billing->method('getEmail')->willReturn($email);
        $billing->method('getFirstname')->willReturn($firstName);
        $billing->method('getLastname')->willReturn($lastName);

        $siteCode = 'aaa';
        $this->helper->method('getSiteCode')->willReturn($siteCode);
        $settlement = 'bbb';
        $this->helper->method('getSettlementCurrency')->willReturn($settlement);
        $languageCode = 'ccc';
        $this->helper->method('getLanguageCode')->willReturn($languageCode);

        $ipAddress = 'ddd';
        $this->helper->method('getIpAddress')->willReturn($ipAddress);
        $sessionId = 'eee';
        $this->session->method('getSessionId')->willReturn($sessionId);
        $userAgent = 'fff';
        $this->helper->method('getUserAgent')->willReturn($userAgent);

        $isAuthorize = 1;
        $this->config->method('isAuthorize')->willReturn($isAuthorize);

        $txnArray = [
            'url'                 => Request::ORDER_URL,
            'token'               => $this->getToken(),
            'amount'              => $buildSubject['amount'],
            'currencyCode'        => $currencyCode,
            'name'                => $firstName . ' ' . $lastName,
            'orderDescription'    => 'Magento 2 Worldpay Payment',
            'customerOrderCode'   => $incrId,
            'siteCode'            => $siteCode,
            'settlementCurrency'  => $settlement,
            'shopperLanguageCode' => $languageCode,
            'authorizeOnly'       => $isAuthorize,
            'is3DSOrder'          => false,
            'shopperEmailAddress' => $email,
            'shopperIpAddress'    => $ipAddress,
            'shopperSessionId'    => $sessionId,
            'shopperUserAgent'    => $userAgent,
            'shopperAcceptHeader' => '*/*'
        ];

        $this->appendAddress($txnArray, $billing, 'billingAddress');
        $this->appendAddress($txnArray, $shipping, 'deliveryAddress');

        $this->assertEquals($txnArray, $this->object->build($buildSubject));
    }

    /**
     * @return string
     */
    private function getToken()
    {
        $tokenRes = ['token' => 'test token'];

        $this->helper->method('sendRequest')->willReturn($tokenRes);

        $this->helper->expects($this->at(0))->method('getInfo')->willReturn($tokenRes['token']);

        return $tokenRes['token'];
    }

    /**
     * @param array $txnArray
     * @param OrderAddressInterface|PHPUnit_Framework_MockObject_MockObject $address
     * @param string $key
     */
    private function appendAddress(&$txnArray, $address, $key)
    {
        $street   = ['street'];
        $postcode = 'postcode';
        $city     = 'city';
        $country  = 'country';
        $address->method('getStreet')->willReturn($street);
        $address->method('getPostcode')->willReturn($postcode);
        $address->method('getCity')->willReturn($city);
        $address->method('getCountryId')->willReturn($country);

        $txnArray[$key] = [
            'address1'    => $street[0],
            'postalCode'  => $postcode,
            'city'        => $city,
            'countryCode' => $country,
        ];
    }
}
