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

namespace Mageplaza\Worldpay\Gateway\Request;

use InvalidArgumentException;
use Magento\Checkout\Model\Session;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Vault\Model\Ui\VaultConfigProvider;
use Mageplaza\Worldpay\Gateway\Config\Cards;
use Mageplaza\Worldpay\Helper\Request;
use Mageplaza\Worldpay\Helper\Response;

/**
 * Class AbstractRequest
 * @package Mageplaza\Worldpay\Gateway\Request
 */
abstract class AbstractRequest
{
    /**
     * @var Request
     */
    protected $helper;

    /**
     * @var Response
     */
    protected $responseHelper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Cards
     */
    protected $config;

    /**
     * AbstractRequest constructor.
     *
     * @param Request $helper
     * @param Response $responseHelper
     * @param Session $session
     * @param Cards $config
     */
    public function __construct(
        Request $helper,
        Response $responseHelper,
        Session $session,
        Cards $config
    ) {
        $this->helper         = $helper;
        $this->responseHelper = $responseHelper;
        $this->session        = $session;
        $this->config         = $config;
    }

    /**
     * @param array $buildSubject
     *
     * @return InfoInterface
     */
    protected function getValidPaymentInstance(array $buildSubject)
    {
        $paymentDataObject = SubjectReader::readPayment($buildSubject);

        $payment = $paymentDataObject->getPayment();

        ContextHelper::assertOrderPayment($payment);

        return $payment;
    }

    /**
     * @param array $buildSubject
     *
     * @return array
     */
    protected function prepareTxnArray($buildSubject)
    {
        /** @var Payment $payment */
        $payment  = $this->getValidPaymentInstance($buildSubject);
        $order    = $payment->getOrder();
        $billing  = $order->getBillingAddress();
        $currency = $order->getOrderCurrencyCode();

        $txnArray = [
            'url'                 => Request::ORDER_URL,
            'token'               => $this->getToken($payment->getAdditionalInformation()),
            'amount'              => $this->helper->convertAmount($buildSubject['amount'], $order),
            'currencyCode'        => $currency,
            'name'                => $billing ? $billing->getFirstname() . ' ' . $billing->getLastname() : '',
            'orderDescription'    => 'Magento 2 Worldpay Payment',
            'customerOrderCode'   => $order->getIncrementId(),
            'siteCode'            => $this->helper->getSiteCode($currency),
            'settlementCurrency'  => $this->helper->getSettlementCurrency($currency),
            'shopperLanguageCode' => $this->helper->getLanguageCode(),
            'authorizeOnly'       => $this->config->isAuthorize(),
            'is3DSOrder'          => false,
            'shopperEmailAddress' => $billing ? $billing->getEmail() : '',
            'shopperIpAddress'    => $this->helper->getIpAddress(),
            'shopperSessionId'    => $this->session->getSessionId(),
            'shopperUserAgent'    => $this->helper->getUserAgent(),
            'shopperAcceptHeader' => '*/*'
        ];

        $this->appendAddress($txnArray, $billing, 'billingAddress');
        $this->appendAddress($txnArray, $order->getShippingAddress(), 'deliveryAddress');

        $payment->unsAdditionalInformation();

        return $txnArray;
    }

    /**
     * @param array $info
     *
     * @return string
     */
    protected function getToken($info)
    {
        if ($token = $this->helper->getInfo($info, 'token')) {
            return $token;
        }

        $tokenRes = $this->helper->sendRequest(Request::TOKEN_URL, [
            'reusable'      => (bool) $this->helper->getInfo($info, VaultConfigProvider::IS_ACTIVE_CODE),
            'paymentMethod' => [
                'name'        => $this->helper->getInfo($info, 'cc_holder'),
                'expiryMonth' => $this->helper->getInfo($info, 'cc_exp_month'),
                'expiryYear'  => $this->helper->getInfo($info, 'cc_exp_year'),
                'cardNumber'  => $this->helper->getInfo($info, 'cc_number'),
                'cvc'         => $this->helper->getInfo($info, 'cc_cid'),
                'type'        => 'Card',
            ],
            'clientKey'     => $this->helper->getClientKey()
        ]);

        if ($error = $this->responseHelper->hasError($tokenRes)) {
            throw new InvalidArgumentException(__($error));
        }

        return $this->helper->getInfo($tokenRes, 'token');
    }

    /**
     * @param array $txnArray
     * @param OrderAddressInterface $address
     * @param string $key
     */
    protected function appendAddress(&$txnArray, $address, $key)
    {
        if (!$address) {
            return;
        }

        $txnArray[$key] = [
            'address1'    => count($address->getStreet()) ? $address->getStreet()[0] : null,
            'postalCode'  => $address->getPostcode(),
            'city'        => $address->getCity(),
            'countryCode' => $address->getCountryId(),
        ];
    }
}
