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
 * Class OrderStatus
 * @package Mageplaza\Worldpay\Model\Source
 */
class OrderStatus extends AbstractSource
{
    const PROCESSING = 'processing';
    const FRAUD      = 'fraud';

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::PROCESSING => __('Processing'),
            self::FRAUD      => __('Suspected Fraud'),
        ];
    }
}
