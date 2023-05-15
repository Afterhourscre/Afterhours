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
 * Class CardType
 * @package Mageplaza\Worldpay\Model\Source
 */
class CardType extends AbstractSource
{
    const VISA             = 'VI';
    const MASTERCARD       = 'MC';
    const MAESTRO_IN       = 'MI';
    const MAESTRO_DO       = 'MD';
    const AMERICAN_EXPRESS = 'AE';

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::VISA             => __('Visa'),
            self::MASTERCARD       => __('Mastercard'),
            self::MAESTRO_IN       => __('Maestro'),
            self::AMERICAN_EXPRESS => __('American Express'),
        ];
    }
}
