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
 * Class Locale
 * @package Mageplaza\Barclaycard\Model\Source
 */
class Locale extends \Magento\Config\Model\Config\Source\Locale
{
    const ALLOWED = 'ar_AR,cs_CZ,da_DK,de_DE,el_GR,en_US,es_ES,fi_FI,fr_FR,he_IL,hu_HU,it_IT,ja_JP,ko_KR,nl_BE,nl_NL,
    no_NO,pl_PL,pt_PT,ru_RU,se_SE,sk_SK,tr_TR,zh_CN';

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
