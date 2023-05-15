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

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment;
use Mageplaza\Worldpay\Model\Source\CardTypeMapper;
use Mageplaza\Worldpay\Model\Source\PaymentInfo as Info;

/**
 * Class Response
 * @package Mageplaza\Worldpay\Helper
 */
class Response extends Data
{
    /**
     * @param Payment $payment
     * @param array $response
     *
     * @return InfoInterface|Payment
     * @throws LocalizedException
     */
    public function handleResponse($payment, $response)
    {
        $payment->unsAdditionalInformation();

        if (empty($response)) {
            return $payment;
        }

        if ($txnId = $this->getInfo($response, 'orderCode')) {
            $payment->setTransactionId($txnId);
        }

        $payment->setAdditionalInformation(Info::TXN_ID, $txnId);
        $payment->setAdditionalInformation(Info::ORDER_TOKEN, $this->getInfo($response, 'token'));

        if ($card = $this->getInfo($response, 'paymentResponse')) {
            $expiryMonth = $this->getInfo($card, 'expiryMonth');
            $expiryYear  = $this->getInfo($card, 'expiryYear');

            $payment->setAdditionalInformation(Info::CARD_TYPE, $this->getInfo($card, 'cardType'));
            $payment->setAdditionalInformation(Info::LAST_CARD_4, $this->getInfo($card, 'maskedCardNumber'));
            $payment->setAdditionalInformation(Info::EXPIRY_MONTH, $expiryMonth);
            $payment->setAdditionalInformation(Info::EXPIRY_YEAR, $expiryYear);

            if ($expiryMonth || $expiryYear) {
                $payment->setAdditionalInformation(Info::EXPIRY_DATE, $expiryMonth . '/' . $expiryYear);
            }

            $payment->setAdditionalInformation(Info::PAYMENT_TYPE, $this->getInfo($card, 'type'));
            $payment->setAdditionalInformation(Info::APM_TYPE, $this->getInfo($card, 'apmName'));
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
        if ($response === false) {
            return (string) __('Internal Server error. Please try again later.');
        }

        if (empty($response)) {
            return false;
        }

        if (!$cardType = $this->getInfo($response, ['paymentResponse', 'cardType'])) {
            $cardType = $this->getInfo($response, ['paymentMethod', 'cardType']);
        }

        if ($cardType && !in_array(CardTypeMapper::getCardType($cardType), $this->config->getCcTypes(), true)) {
            return (string) __('Card type "%1" is not allowed.', $cardType);
        }

        $outcome = $this->getInfo($response, 'httpStatusCode');
        $status  = $this->getInfo($response, 'paymentStatus');
        $valid   = ['PRE_AUTHORIZED', 'AUTHORIZED', 'SUCCESS'];

        if (($outcome && $outcome === 200) || ($status && in_array($status, $valid, true))) {
            return false;
        }

        $message = '';

        $this->appendMessage($message, $outcome ?: $status, 'Status ');
        $this->appendMessage($message, $this->getInfo($response, 'message'));
        $this->appendMessage($message, $this->getInfo($response, 'description'));
        $this->appendMessage($message, $this->getInfo($response, 'iso8583Status'), 'ISO 8583 #');

        return $message;
    }

    /**
     * @param string $message
     * @param string $string
     * @param string $prefix
     */
    protected function appendMessage(&$message, $string, $prefix = '')
    {
        if ($string) {
            if ($message) {
                $message .= ' - ';
            }

            $message .= $prefix . $string;
        }
    }
}
