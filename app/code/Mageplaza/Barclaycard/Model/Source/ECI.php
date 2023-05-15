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
 * Class ECI
 * Electronic Commerce Indicator
 * @package Mageplaza\Barclaycard\Model\Source
 */
class ECI extends AbstractSource
{
    const MOTO = 1;
    const ECOM = 7;

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::MOTO => __('Manually keyed (MOTO) (card not present)'),
            self::ECOM => __('E-commerce with SSL encryption'),
        ];
    }
}
