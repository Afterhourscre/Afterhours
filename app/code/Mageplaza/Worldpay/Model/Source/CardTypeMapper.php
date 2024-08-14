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
 * Class CardTypeMapper
 * @package Mageplaza\Worldpay\Model\Source
 */
class CardTypeMapper extends AbstractSource
{
    const VISA_CREDIT            = 'VISA_CREDIT';
    const VISA_DEBIT             = 'VISA_DEBIT';
    const VISA_CORP_CREDIT       = 'VISA_CORPORATE_CREDIT';
    const VISA_CORP_DEBIT        = 'VISA_CORPORATE_DEBIT';
    const MASTERCARD_CREDIT      = 'MASTERCARD_CREDIT';
    const MASTERCARD_DEBIT       = 'MASTERCARD_DEBIT';
    const MASTERCARD_CORP_CREDIT = 'MASTERCARD_CORPORATE_CREDIT';
    const MASTERCARD_CORP_DEBIT  = 'MASTERCARD_CORPORATE_DEBIT';
    const MAESTRO                = 'MAESTRO';
    const AMERICAN_EXPRESS       = 'AMEX';
    const MASTERCARD             = 'MASTERCARD';

    /**
     * Retrieve option array
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::VISA_CREDIT            => CardType::VISA,
            self::VISA_DEBIT             => CardType::VISA,
            self::VISA_CORP_CREDIT       => CardType::VISA,
            self::VISA_CORP_DEBIT        => CardType::VISA,
            self::MASTERCARD_CREDIT      => CardType::MASTERCARD,
            self::MASTERCARD_DEBIT       => CardType::MASTERCARD,
            self::MASTERCARD_CORP_CREDIT => CardType::MASTERCARD,
            self::MASTERCARD_CORP_DEBIT  => CardType::MASTERCARD,
            self::MAESTRO                => CardType::MAESTRO_IN,
            self::AMERICAN_EXPRESS       => CardType::AMERICAN_EXPRESS,
            self::MASTERCARD      => CardType::MASTERCARD
        ];
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function getCardType($value)
    {
        $options = self::getOptionArray();

        return isset($options[$value]) ? $options[$value] : $value;
    }

    /**
     * @param array $cctypes
     *
     * @return string
     */
    public static function getAllCardTypes($cctypes)
    {
        return array_filter(self::getOptionArray(), function ($option) use ($cctypes) {
            foreach ($cctypes as $type) {
                if ($option === $type) {
                    return true;
                }
            }

            return false;
        });
    }
}
