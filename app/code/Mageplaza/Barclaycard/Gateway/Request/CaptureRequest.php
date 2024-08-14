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
use Magento\Payment\Gateway\Request\BuilderInterface;
use Mageplaza\Barclaycard\Helper\Request;
use Mageplaza\Barclaycard\Model\Source\PaymentInfo;

/**
 * Class CaptureRequest
 * @package Mageplaza\Barclaycard\Gateway\Request
 */
class CaptureRequest extends AbstractRequest implements BuilderInterface
{
    /**
     * Builds request
     *
     * @param array $buildSubject
     *
     * @return array
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        $payment = $this->helper->getValidPaymentInstance($buildSubject);

        if ($txnId = $payment->getAdditionalInformation(PaymentInfo::TXN_ID)) {
            return array_merge($this->getCredentialsArray($this->helper->getApiUrl(Request::MAINT), 'SAS'), [
                'PAYID' => $txnId,
                'EMAIL' => $payment->getOrder()->getCustomerEmail(),
            ]);
        }

        return $this->buildTxnArray($buildSubject);
    }
}
