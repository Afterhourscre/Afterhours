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

namespace Mageplaza\Worldpay\Model\Api;

use InvalidArgumentException;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Vault\Model\Ui\VaultConfigProvider;
use Mageplaza\Worldpay\Api\Data\OrderResponseInterface;
use Mageplaza\Worldpay\Api\Data\OrderResponseInterfaceFactory;
use Mageplaza\Worldpay\Api\PaymentInterface;
use Mageplaza\Worldpay\Gateway\Config\Cards;
use Mageplaza\Worldpay\Helper\Request;
use Mageplaza\Worldpay\Helper\Response;

/**
 * Class Payment
 * @package Mageplaza\Worldpay\Model
 */
class Payment implements PaymentInterface
{
    /**
     * @var Request
     */
    private $helper;

    /**
     * @var Response
     */
    private $responseHelper;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Cards
     */
    private $config;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var Logger
     */
    private $paymentLogger;

    /**
     * @var OrderResponseInterfaceFactory
     */
    private $orderResponseFactory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Payment constructor.
     *
     * @param Request $helper
     * @param Response $responseHelper
     * @param Session $session
     * @param Cards $config
     * @param CartRepositoryInterface $cartRepository
     * @param Logger $paymentLogger
     * @param OrderResponseInterfaceFactory $orderResponseFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Request $helper,
        Response $responseHelper,
        Session $session,
        Cards $config,
        CartRepositoryInterface $cartRepository,
        Logger $paymentLogger,
        OrderResponseInterfaceFactory $orderResponseFactory,
        UrlInterface $urlBuilder
    ) {
        $this->helper               = $helper;
        $this->responseHelper       = $responseHelper;
        $this->session              = $session;
        $this->config               = $config;
        $this->cartRepository       = $cartRepository;
        $this->paymentLogger        = $paymentLogger;
        $this->orderResponseFactory = $orderResponseFactory;
        $this->urlBuilder           = $urlBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function apply3ds($cartId)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        $txnArray = array_merge($this->prepareTxnArray($quote), [
            'token'         => $this->getToken($quote->getPayment()->getAdditionalInformation()),
            'name'          => $this->helper->isTestEnv() ? '3D' : $quote->getBillingAddress()->getName(),
            'authorizeOnly' => $this->config->isAuthorize(),
            'is3DSOrder'    => true,
        ]);

        $response = $this->helper->sendRequest(Request::ORDER_URL, $txnArray);

        $this->paymentLogger->debug(['apply 3ds request' => $txnArray, 'apply 3ds response' => $response]);

        return $this->prepareResponse($quote, $response);
    }

    /**
     * {@inheritDoc}
     */
    public function processApm($cartId, $token)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        $txnArray = array_merge($this->prepareTxnArray($quote), [
            'token'      => $token,
            'name'       => $quote->getBillingAddress()->getName(),
            'successUrl' => $this->urlBuilder->getUrl('mpworldpay/apm/success', ['_secure' => true]),
            'pendingUrl' => $this->urlBuilder->getUrl('mpworldpay/apm/pending', ['_secure' => true]),
            'failureUrl' => $this->urlBuilder->getUrl('mpworldpay/apm/failure', ['_secure' => true]),
            'cancelUrl'  => $this->urlBuilder->getUrl('mpworldpay/apm/cancel', ['_secure' => true]),
            'errorUrl'   => $this->urlBuilder->getUrl('mpworldpay/apm/error', ['_secure' => true]),
        ]);

        $response = $this->helper->sendRequest(Request::ORDER_URL, $txnArray);

        $this->config->setMethodCode($quote->getPayment()->getMethod());
        $this->paymentLogger->debug(['process apm request' => $txnArray, 'process apm response' => $response]);

        return $this->prepareResponse($quote, $response);
    }

    /**
     * @param Quote $quote
     *
     * @return array
     */
    protected function prepareTxnArray($quote)
    {
        $currency = $quote->getQuoteCurrencyCode();

        if (!$quote->getReservedOrderId()) {
            $quote->reserveOrderId();
        }

        $txnArray = [
            'amount'              => $this->helper->formatAmount($quote->getGrandTotal(), $currency),
            'currencyCode'        => $currency,
            'orderDescription'    => 'Magento 2 Worldpay Payment',
            'customerOrderCode'   => $quote->getReservedOrderId(),
            'siteCode'            => $this->helper->getSiteCode($currency),
            'settlementCurrency'  => $this->helper->getSettlementCurrency($currency),
            'shopperLanguageCode' => $this->helper->getLanguageCode(),
            'shopperEmailAddress' => $quote->getBillingAddress()->getEmail(),
            'shopperIpAddress'    => $this->helper->getIpAddress(),
            'shopperSessionId'    => $this->session->getSessionId(),
            'shopperUserAgent'    => $this->helper->getUserAgent(),
            'shopperAcceptHeader' => '*/*',
        ];

        $this->appendAddress($txnArray, $quote->getBillingAddress(), 'billingAddress');
        $this->appendAddress($txnArray, $quote->getShippingAddress(), 'deliveryAddress');

        return $txnArray;
    }

    /**
     * @param Quote $quote
     * @param array $response
     *
     * @return OrderResponseInterface
     * @throws LocalizedException
     */
    protected function prepareResponse($quote, $response)
    {
        $payment = $quote->getPayment();

        /** @var OrderResponseInterface $orderRes */
        $orderRes = $this->orderResponseFactory->create();

        if ($error = $this->responseHelper->hasError($response)) {
            $payment->unsAdditionalInformation();
            $orderRes->setMessage($error);
        } else {
            $payment->setAdditionalInformation('hostedResponse', $response);
            $orderRes->setRedirectUrl($this->helper->getInfo($response, 'redirectURL'))
                ->setPaReq($this->helper->getInfo($response, 'oneTime3DsToken'));
        }

        $this->cartRepository->save($quote);

        return $orderRes;
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
     * @param Address $address
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
