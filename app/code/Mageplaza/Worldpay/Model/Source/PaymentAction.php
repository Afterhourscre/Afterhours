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
 * Class PaymentAction
 * @package Mageplaza\Worldpay\Model\Source
 */
class PaymentAction extends AbstractSource
{
    const ACTION_AUTHORIZE         = 'authorize';
    const ACTION_AUTHORIZE_CAPTURE = 'authorize_capture';

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::ACTION_AUTHORIZE         => __('Authorize'),
            self::ACTION_AUTHORIZE_CAPTURE => __('Authorize and Capture'),
        ];
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function getPaymentType($value)
    {
        switch ($value) {
            case self::ACTION_AUTHORIZE:
                return 'D';
            case self::ACTION_AUTHORIZE_CAPTURE:
                return 'C';
            default:
                return $value;
        }
    }
}
