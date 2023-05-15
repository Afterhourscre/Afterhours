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
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CallForPrice\Model;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class QuoteFields
 * @package Mageplaza\CallForPrice\Model
 */
class QuoteFields implements ArrayInterface
{
    const NAME  = 'name';
    const EMAIL = 'email';
    const PHONE = 'phone';
    const NOTE  = 'note';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::NAME,
                'label' => __('Name')
            ],
            [
                'value' => self::EMAIL,
                'label' => __('Email')
            ],
            [
                'value' => self::PHONE,
                'label' => __('Phone')
            ],
            [
                'value' => self::NOTE,
                'label' => __('Note')
            ],
        ];

        return $options;
    }
}
