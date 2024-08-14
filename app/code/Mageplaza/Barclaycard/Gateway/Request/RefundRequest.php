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

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order;
use Mageplaza\Barclaycard\Helper\Request;
use Mageplaza\Barclaycard\Model\Source\PaymentInfo;

/**
 * Class RefundRequest
 * @package Mageplaza\Barclaycard\Gateway\Request
 */
class RefundRequest extends AbstractRequest implements BuilderInterface
{
    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     *
     * @return array
     */
    public function build(array $buildSubject)
    {
        $payment = $this->helper->getValidPaymentInstance($buildSubject);
        $order   = $payment->getOrder();

        $amount = $this->helper->convertAmount($buildSubject['amount'], $order);
        $remain = $this->getRemainAmount($order);

        $url       = $this->helper->getApiUrl(Request::MAINT);
        $operation = $amount === $remain ? 'RFS' : 'RFD';

        return array_merge($this->getCredentialsArray($url, $operation), [
            'AMOUNT' => min($amount, $remain),
            'PAYID'  => $payment->getAdditionalInformation(PaymentInfo::TXN_ID),
            'EMAIL'  => $order->getCustomerEmail(),
        ]);
    }

    /**
     * @param Order $order
     *
     * @return float
     */
    private function getRemainAmount($order)
    {
        $total = $order->getGrandTotal();

        if ($creditMemos = $order->getCreditmemosCollection()) {
            foreach ($creditMemos->getItems() as $item) {
                $total -= $item->getGrandTotal();
            }
        }

        return number_format($total, 2, '.', '') * 100;
    }
}
