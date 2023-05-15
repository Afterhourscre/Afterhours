<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\OnSale\Model\Source\Label\Renderer;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Placement
 *
 * @package Aheadworks\OnSale\Model\Source\Label\Renderer
 */
class Placement implements OptionSourceInterface
{
    /**#@+
     * Placement values
     */
    const PRODUCT_LIST = 'product_list';
    const PRODUCT_PAGE = 'product_page';
    const MINICART = 'minicart';
    const CART = 'cart';
    const CHECKOUT = 'checkout';
    const WISHLIST_SIDEBAR = 'wishlist_sidebar';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::PRODUCT_LIST,
                'label' => __('Product List')
            ],
            [
                'value' => self::PRODUCT_PAGE,
                'label' => __('Product Page')
            ],
            [
                'value' => self::MINICART,
                'label' => __('Minicart')
            ],
            [
                'value' => self::CART,
                'label' => __('Cart')
            ],
            [
                'value' => self::CHECKOUT,
                'label' => __('Checkout')
            ],
            [
                'value' => self::WISHLIST_SIDEBAR,
                'label' => __('Wishlist Sidebar')
            ],
        ];
    }
}
