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
 * Class Action
 * @package Mageplaza\CallForPrice\Model
 */
class Action implements ArrayInterface
{
    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => 'popup_quote_form',
                'label' => __('Popup a quote form')
            ],
            [
                'value' => 'redirect_url',
                'label' => __('Redirect to an URL')
            ],
            [
                'value' => 'login_see_price',
                'label' => __('Login to See Price')
            ],
            [
                'value' => 'hide_add_to_cart',
                'label' => __('Hide Add To Cart Button')
            ],
        ];

        return $options;
    }
}
