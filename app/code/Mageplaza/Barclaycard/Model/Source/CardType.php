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
 * Class CardType
 * @package Mageplaza\Barclaycard\Model\Source
 */
class CardType extends AbstractSource
{
    const VISA             = 'VI';
    const MASTERCARD       = 'MC';
    const AMERICAN_EXPRESS = 'AE';
    const MAESTRO          = 'MD';
    const DINERS           = 'DN';
    const JCB              = 'JCB';

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::VISA             => __('Visa'),
            self::MASTERCARD       => __('Mastercard'),
            self::AMERICAN_EXPRESS => __('American Express'),
            self::MAESTRO          => __('Maestro'),
            self::DINERS           => __('Diners Club'),
            self::JCB              => __('JCB'),
        ];
    }
}
