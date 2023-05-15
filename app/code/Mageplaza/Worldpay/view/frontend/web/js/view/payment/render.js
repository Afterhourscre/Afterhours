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

define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push(
        {
            type: 'mpworldpay_cards',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/cards'
        },
        {
            type: 'mpworldpay_paypal',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/apm'
        },
        {
            type: 'mpworldpay_alipay',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/apm'
        },
        {
            type: 'mpworldpay_giropay',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/giropay'
        },
        {
            type: 'mpworldpay_ideal',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/ideal'
        },
        {
            type: 'mpworldpay_mistercash',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/apm'
        },
        {
            type: 'mpworldpay_paysafecard',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/apm'
        },
        {
            type: 'mpworldpay_postepay',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/apm'
        },
        {
            type: 'mpworldpay_przelewy24',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/apm'
        },
        {
            type: 'mpworldpay_qiwi',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/apm'
        },
        {
            type: 'mpworldpay_sofort',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/apm'
        },
        {
            type: 'mpworldpay_yandex',
            component: 'Mageplaza_Worldpay/js/view/payment/method-renderer/apm'
        }
    );

    return Component.extend({});
});
