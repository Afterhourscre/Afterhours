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

namespace Mageplaza\Worldpay\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Model\Order\Payment;
use Mageplaza\Worldpay\Helper\Response;
use Mageplaza\Worldpay\Model\Source\OrderStatus;

/**
 * Class AbstractResponseHandler
 * @package Mageplaza\Worldpay\Gateway\Response
 */
abstract class AbstractResponseHandler
{
    /**
     * @var Response
     */
    protected $helper;

    /**
     * AbstractResponseHandler constructor.
     *
     * @param Response $helper
     */
    public function __construct(Response $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param array $buildSubject
     *
     * @return InfoInterface|Payment
     */
    protected function getValidPaymentInstance(array $buildSubject)
    {
        $paymentDataObject = SubjectReader::readPayment($buildSubject);

        $payment = $paymentDataObject->getPayment();

        ContextHelper::assertOrderPayment($payment);

        return $payment;
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     * @param bool $isClosed
     *
     * @throws LocalizedException
     */
    protected function handleResponse($handlingSubject, $response, $isClosed)
    {
        $payment = $this->helper->handleResponse($this->getValidPaymentInstance($handlingSubject), $response);
        $payment->setIsTransactionClosed($isClosed);

        $isFraud = $payment->getMethodInstance()->getConfigData('order_status') === OrderStatus::FRAUD;
        $payment->setIsFraudDetected($isFraud);
        $payment->setIsTransactionPending($isFraud);
    }
}
