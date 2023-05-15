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

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment;
use Mageplaza\Barclaycard\Model\Source\PaymentInfo as Info;

/**
 * Class Response
 * @package Mageplaza\Barclaycard\Helper
 */
class Response extends Data
{
    /**
     * @param Payment $payment
     * @param array $response
     *
     * @return Payment
     * @throws LocalizedException
     */
    public function handleResponse($payment, $response)
    {
        $payment->unsAdditionalInformation('hostedResponse');

        $response = $response['@attributes'];

        if ($txnId = $this->getInfo($response, 'PAYID')) {
            $payment->setTransactionId($txnId);
        }

        $info = [
            Info::TXN_ID      => $txnId,
            Info::CC_TYPE     => $this->getInfo($response, 'BRAND'),
            Info::CC_LAST_4   => $this->getInfo($response, 'CARDNO'),
            Info::CC_EXP_DATE => $this->getInfo($response, 'ED'),
            Info::ORDER_ID    => $this->getInfo($response, 'orderID'),
            Info::STATUS      => $this->getInfo($response, 'STATUS'),
            Info::AAV_CHECK   => $this->getInfo($response, 'AAVCheck'),
            Info::CVC_CHECK   => $this->getInfo($response, 'CVCCheck'),
        ];

        foreach ($info as $key => $value) {
            if ($value) {
                $payment->setAdditionalInformation($key, $value);
            }
        }

        return $payment;
    }

    /**
     * @param array $response
     *
     * @return string|bool
     */
    public function hasError($response)
    {
        if (empty($response['@attributes'])) {
            return (string) __('Response does not exist');
        }

        $response = $response['@attributes'];

        if ($error = $this->verifyShaOut($response)) {
            return $error;
        }

        switch ((int) $this->getInfo($response, 'STATUS')) {
            case 0:
                $message = __('Transaction invalid or incomplete');
                break;
            case 1:
                $message = __('Transaction is cancelled');
                break;
            case 2:
                $message = __('Authorisation is refused');
                break;
            default:
                return false;
        }

        $this->appendMessage($message, $this->getInfo($response, 'NCERROR'), __('Error Code'));
        $this->appendMessage($message, $this->getInfo($response, 'NCERRORPLUS'));

        return (string) $message;
    }

    /**
     * @param array $response
     *
     * @return bool|string
     */
    private function verifyShaOut($response)
    {
        if (empty($response['SHASIGN'])) {
            return false;
        }

        $shaSign = $response['SHASIGN'];

        $this->appendShaSign($response, $this->config->getShaOut());

        if ($shaSign !== $response['SHASIGN']) {
            return (string ) __('Invalid response');
        }

        return false;
    }

    /**
     * @param string $message
     * @param string $string
     * @param string $prefix
     */
    public function appendMessage(&$message, $string, $prefix = '')
    {
        if ($string) {
            if ($message) {
                $message .= ' - ';
            }
            if ($prefix) {
                $prefix .= ' ';
            }

            $message .= $prefix . $string;
        }
    }
}
