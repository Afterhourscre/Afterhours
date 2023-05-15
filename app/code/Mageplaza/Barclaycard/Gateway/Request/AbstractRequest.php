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

namespace Mageplaza\Barclaycard\Gateway\Request;

use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Barclaycard\Gateway\Config\Direct;
use Mageplaza\Barclaycard\Helper\Request;

/**
 * Class AbstractRequest
 * @package Mageplaza\Barclaycard\Gateway\Request
 */
abstract class AbstractRequest
{
    /**
     * @var Request
     */
    protected $helper;

    /**
     * @var Direct
     */
    protected $config;

    /**
     * AbstractRequest constructor.
     *
     * @param Request $helper
     * @param Direct $config
     */
    public function __construct(
        Request $helper,
        Direct $config
    ) {
        $this->helper = $helper;
        $this->config = $config;
    }

    /**
     * @param string $url
     * @param string $operation
     *
     * @return array
     */
    protected function getCredentialsArray($url, $operation)
    {
        return [
            'url'       => $url,
            'OPERATION' => $operation,
            'PSPID'     => $this->helper->getPspId(),
            'PSWD'      => $this->config->getPassword(),
            'USERID'    => $this->config->getUserId(),
        ];
    }

    /**
     * @param array $buildSubject
     *
     * @return array
     * @throws LocalizedException
     */
    protected function buildTxnArray($buildSubject)
    {
        $payment = $this->helper->getValidPaymentInstance($buildSubject);
        $order   = $payment->getOrder();

        $url       = $this->helper->getApiUrl(Request::DIRECT);
        $operation = $this->config->getPaymentActionMapper();
        $isFE      = $payment->getAdditionalInformation('is_frontend');

        $data = array_merge($this->getCredentialsArray($url, $operation), $this->helper->getCardInfo($payment, $isFE), [
            'ORDERID'     => date('dmy-Gis') . '-' . $order->getQuoteId(),
            'AMOUNT'      => $this->helper->convertAmount($buildSubject['amount'], $order),
            'CURRENCY'    => $order->getOrderCurrencyCode(),
            'REMOTE_ADDR' => $this->helper->getRemoteIpAddress(),
            'EMAIL'       => $order->getCustomerEmail(),
            'EXCLPMLIST'  => $this->config->getExclCcTypes(),
            'FLAG3D'      => 'N',

            '3DS_EXEMPTION_INDICATOR' => '05', // Merchant/Acquirer Transaction risk analysis
        ]);

        return $this->helper->prepareBody($data);
    }
}
