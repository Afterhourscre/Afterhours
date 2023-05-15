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

namespace Mageplaza\Barclaycard\Model\Source;

/**
 * Class PaymentInfo
 * @package Mageplaza\Barclaycard\Model\Source
 */
class PaymentInfo extends AbstractSource
{
    const TXN_ID = 'txn_id';

    const CC_TYPE     = 'cc_type';
    const CC_LAST_4   = 'cc_last_4';
    const CC_EXP_DATE = 'cc_exp_date';

    const ORDER_ID  = 'order_id';
    const CVC_CHECK = 'cvc_check';
    const AAV_CHECK = 'aav_check';
    const STATUS    = 'status';

    /**
     * @param bool $showAll
     *
     * @return array
     */
    public static function getOptionArray($showAll = true)
    {
        $frontend = [
            self::CC_TYPE     => __('Card Type'),
            self::CC_LAST_4   => __('Last Card Number'),
            self::CC_EXP_DATE => __('Expiration Date'),
        ];

        if (!$showAll) {
            return $frontend;
        }

        return array_merge($frontend, [
            self::TXN_ID    => __('Transaction ID'),
            self::STATUS    => __('Status'),
            self::ORDER_ID  => __('Order ID'),
            self::CVC_CHECK => __('CVC Response'),
            self::AAV_CHECK => __('AAV Response'),
        ]);
    }
}
