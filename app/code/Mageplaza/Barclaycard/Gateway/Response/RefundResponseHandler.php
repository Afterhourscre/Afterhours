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

use Magento\Payment\Gateway\Response\HandlerInterface;

/**
 * Class RefundResponseHandler
 * @package Mageplaza\Barclaycard\Gateway\Response
 */
class RefundResponseHandler extends AbstractResponseHandler implements HandlerInterface
{
    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $payment = $this->helper->getValidPaymentInstance($handlingSubject);
        $payment->setIsTransactionClosed(true);

        if ($txnId = $this->helper->getInfo($response, 'transactionId')) {
            $payment->setTransactionId($txnId);
        }
    }
}
