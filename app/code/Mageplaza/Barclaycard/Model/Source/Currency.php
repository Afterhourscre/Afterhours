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
 * Class Currency
 * @package Mageplaza\Barclaycard\Model\Source
 */
class Currency extends \Magento\Config\Model\Config\Source\Locale\Currency
{
    const ALLOWED = 'ALL,ARS,AUD,BOB,BGN,BRL,CAD,CLP,CNY,COP,HRK,CZK,DKK,EGP,EUR,HKD,HUF,ISK,INR,IDR,ILS,JPY,KRW,MYR,
    MYR,MAD,NPR,NZD,NGN,NOK,PKR,PYG,PEN,PEN,PLN,QAR,RON,RUB,SAR,SGD,ZAR,LKR,SEK,CHF,TWD,THB,TRY,UAH,GBP,AED,UYU,USD';

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $allow = explode(',', self::ALLOWED);

        return array_filter(parent::toOptionArray(), function ($option) use ($allow) {
            return in_array($option['value'], $allow, true);
        });
    }
}
