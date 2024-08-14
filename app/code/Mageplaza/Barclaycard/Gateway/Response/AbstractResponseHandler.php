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

namespace Mageplaza\Barclaycard\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Barclaycard\Helper\Response;
use Mageplaza\Barclaycard\Model\Source\OrderStatus;

/**
 * Class AbstractResponseHandler
 * @package Mageplaza\Barclaycard\Gateway\Response
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
     * @param array $handlingSubject
     * @param array $response
     * @param bool $isClosed
     *
     * @throws LocalizedException
     */
    protected function handleResponse($handlingSubject, $response, $isClosed)
    {
        $payment = $this->helper->getValidPaymentInstance($handlingSubject);

        $isFraud = $payment->getMethodInstance()->getConfigData('order_status') === OrderStatus::FRAUD;
        $payment->setIsFraudDetected($isFraud);
        $payment->setIsTransactionPending($isFraud);
        $payment->setIsTransactionClosed($isClosed);

        $this->helper->handleResponse($payment, $response);
    }
}
