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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Barclaycard\Model\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\Barclaycard\Api\PaymentInterface;
use Mageplaza\Barclaycard\Gateway\Config\Direct;
use Mageplaza\Barclaycard\Gateway\Config\Hosted;
use Mageplaza\Barclaycard\Helper\Request;
use Mageplaza\Barclaycard\Helper\Response;

/**
 * Class Payment
 * @package Mageplaza\Barclaycard\Model
 */
class Payment implements PaymentInterface
{
    /**
     * @var Request
     */
    private $helper;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Hosted
     */
    private $hostedConfig;

    /**
     * @var Direct
     */
    private $directConfig;

    /**
     * @var Response
     */
    private $responseHelper;

    /**
     * Payment constructor.
     *
     * @param Request $helper
     * @param Response $responseHelper
     * @param CartRepositoryInterface $cartRepository
     * @param UrlInterface $urlBuilder
     * @param Hosted $hostedConfig
     * @param Direct $directConfig
     */
    public function __construct(
        Request $helper,
        Response $responseHelper,
        CartRepositoryInterface $cartRepository,
        UrlInterface $urlBuilder,
        Hosted $hostedConfig,
        Direct $directConfig
    ) {
        $this->helper         = $helper;
        $this->cartRepository = $cartRepository;
        $this->urlBuilder     = $urlBuilder;
        $this->hostedConfig   = $hostedConfig;
        $this->directConfig   = $directConfig;
        $this->responseHelper = $responseHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function getHostedUrl($cartId)
    {
        /** @var Quote $quote */
        $quote   = $this->cartRepository->getActive($cartId);
        $billing = $quote->getBillingAddress();
        $street  = $billing->getStreet();

        $data = array_merge($this->prepareTxnArray($quote, $this->hostedConfig), [
            'LANGUAGE'                        => $this->hostedConfig->getLangCode(),
            'CANCELURL'                       => $this->urlBuilder->getUrl('mpbarclaycard/payment/cancel'),
            'PMLISTTYPE'                      => 0,

            // additional params
            'USERID'                          => $this->hostedConfig->getUserId(),
            'CN'                              => $billing->getFirstname() . ' ' . $billing->getLastname(),
            'OWNERZIP'                        => $billing->getPostcode(),
            'OWNERADDRESS'                    => implode(', ', $street),
            'OWNERCTY'                        => $billing->getCountry(),
            'OWNERTOWN'                       => $billing->getCity(),
            'OWNERTELNO'                      => $billing->getTelephone(),

            // 3D secure params
            'ECOM_BILLTO_POSTAL_CITY'         => $billing->getCity(),
            'ECOM_BILLTO_POSTAL_COUNTRYCODE'  => $billing->getCountry(),
            'ECOM_BILLTO_POSTAL_STREET_LINE1' => $street[0] ?? '',
            'ECOM_BILLTO_POSTAL_STREET_LINE2' => isset($street[1]) ? substr($street[1], 0, 35) : '',
            'ECOM_BILLTO_POSTAL_STREET_LINE3' => isset($street[2]) ? substr($street[2], 0, 35) : '',
            'ECOM_BILLTO_POSTAL_POSTALCODE'   => $billing->getPostcode(),
        ]);

        $body = $this->helper->prepareBody($data);

        $this->helper->appendShaSign($body, $this->hostedConfig->getShaIn());

        /** @var Logger $logger */
        $logger = $this->helper->createObject(Logger::class, ['config' => $this->hostedConfig]);
        $logger->debug(['barclaycard request' => $body]);

        return Request::jsonEncode([
            'action' => $this->helper->getApiUrl(Request::HOSTED),
            'data'   => $body,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function process3DS($cartId)
    {
        /** @var Quote $quote */
        $quote   = $this->cartRepository->getActive($cartId);
        $payment = $quote->getPayment();

        $data = array_merge($this->prepareTxnArray($quote, $this->directConfig), $this->helper->getCardInfo($payment), [
            'USERID'              => $this->directConfig->getUserId(),
            'PSWD'                => $this->directConfig->getPassword(),

            // additional params
            'BROWSERACCEPTHEADER' => 'Accept:*/*',
            'BROWSERCOLORDEPTH'   => 48,
            'BROWSERJAVAENABLED'  => 'true',
            'BROWSERLANGUAGE'     => $this->helper->getLocaleCode(),
            'BROWSERSCREENHEIGHT' => 999999,
            'BROWSERSCREENWIDTH'  => 999999,
            'BROWSERTIMEZONE'     => 0,
            'BROWSERUSERAGENT'    => $this->helper->getUserAgent(),

            // 3D secure params
            'FLAG3D'              => 'Y',
        ]);

        $body = $this->helper->prepareBody($data);

        $this->helper->appendShaSign($body, $this->directConfig->getShaIn());

        /** @var Logger $logger */
        $logger = $this->helper->createObject(Logger::class, ['config' => $this->directConfig]);
        $logger->debug(['barclaycard request' => $body]);

        $response = $this->helper->sendRequest($this->helper->getApiUrl(Request::DIRECT), [], $body);

        if ($error = $this->responseHelper->hasError($response)) {
            $logger->debug(['barclaycard response' => $response]);

            throw new LocalizedException(__($error));
        }

        // return 3ds html form if available
        if (!empty($response['HTML_ANSWER'])) {
            $payment->save();

            return base64_decode($response['HTML_ANSWER']);
        }

        $payment->setAdditionalInformation('hostedResponse', $response)->save();

        return '';
    }

    /**
     * @param Quote $quote
     * @param Hosted|Direct $config
     *
     * @return array
     */
    private function prepareTxnArray($quote, $config)
    {
        return [
            'PSPID'        => $this->helper->getPspId($quote->getStoreId()),
            'ORDERID'      => date('dmy-Gis') . '-' . $quote->getId(),
            'AMOUNT'       => number_format($quote->getGrandTotal(), 2, '.', '') * 100,
            'CURRENCY'     => $quote->getQuoteCurrencyCode(),

            // additional params
            'EMAIL'        => $quote->getCustomerEmail(),
            'OPERATION'    => $config->getPaymentActionMapper(),
            'EXCLPMLIST'   => $config->getExclCcTypes(),

            // 3D secure params
            'WIN3DS'       => 'MAINW',
            'ACCEPTURL'    => $this->urlBuilder->getUrl('mpbarclaycard/payment/accept'),
            'DECLINEURL'   => $this->urlBuilder->getUrl('mpbarclaycard/payment/decline'),
            'EXCEPTIONURL' => $this->urlBuilder->getUrl('mpbarclaycard/payment/exception'),
            'REMOTE_ADDR'  => $this->helper->getRemoteIpAddress(),
        ];
    }
}
