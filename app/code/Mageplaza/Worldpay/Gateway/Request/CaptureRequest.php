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

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Mageplaza\Worldpay\Helper\Request;
use Mageplaza\Worldpay\Model\Source\PaymentInfo;

/**
 * Class CaptureRequest
 * @package Mageplaza\Worldpay\Gateway\Request
 */
class CaptureRequest extends AbstractRequest implements BuilderInterface
{
    /**
     * Builds request
     *
     * @param array $buildSubject
     *
     * @return array
     */
    public function build(array $buildSubject)
    {
        /** @var Payment $payment */
        $payment = $this->getValidPaymentInstance($buildSubject);

        if ($txnId = $payment->getAdditionalInformation(PaymentInfo::TXN_ID)) {
            return [
                'url'           => str_replace('%txn_id%', $txnId, Request::CAPTURE_URL),
                'captureAmount' => ''
            ];
        }

        return $this->prepareTxnArray($buildSubject);
    }
}
