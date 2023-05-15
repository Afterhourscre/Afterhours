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
 * Class CardTypeMapper
 * @package Mageplaza\Barclaycard\Model\Source
 */
class CardTypeMapper extends AbstractSource
{
    const VISA             = 'VISA';
    const MASTERCARD       = 'MasterCard';
    const AMERICAN_EXPRESS = 'AMEX';
    const MAESTRO          = 'Maestro';
    const DINERS           = 'DinersClub';
    const JCB              = 'JCB';

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::VISA             => CardType::VISA,
            self::MASTERCARD       => CardType::MASTERCARD,
            self::AMERICAN_EXPRESS => CardType::AMERICAN_EXPRESS,
            self::MAESTRO          => CardType::MAESTRO,
            self::DINERS           => CardType::DINERS,
            self::JCB              => CardType::JCB,
        ];
    }
}
