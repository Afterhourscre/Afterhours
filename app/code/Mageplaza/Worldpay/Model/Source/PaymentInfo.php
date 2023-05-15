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

namespace Mageplaza\Worldpay\Model\Source;

/**
 * Class PaymentInfo
 * @package Mageplaza\Worldpay\Model\Source
 */
class PaymentInfo extends AbstractSource
{
    const ORDER_ID = 'order_id';
    const TXN_ID   = 'txn_id';

    const CARD_TYPE    = 'mpworldpay_card_type';
    const LAST_CARD_4  = 'mpworldpay_card_last4';
    const EXPIRY_DATE  = 'mpworldpay_expiry_date';
    const EXPIRY_MONTH = 'mpworldpay_expiry_month';
    const EXPIRY_YEAR  = 'mpworldpay_expiry_year';

    const ORDER_TOKEN  = 'mpworldpay_token';
    const PAYMENT_TYPE = 'mpworldpay_type';
    const APM_TYPE     = 'mpworldpay_apm';

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::CARD_TYPE    => __('Card Type'),
            self::LAST_CARD_4  => __('Last Card Number'),
            self::EXPIRY_DATE  => __('Expiration Date'),
            self::TXN_ID       => __('Transaction ID'),
            self::ORDER_TOKEN  => __('Order Token'),
            self::PAYMENT_TYPE => __('Payment Type'),
            self::APM_TYPE     => __('APM Type'),
        ];
    }
}
