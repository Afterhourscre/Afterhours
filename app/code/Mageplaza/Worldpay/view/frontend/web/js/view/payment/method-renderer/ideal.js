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
    'ko',
    '../method-renderer/apm'
], function (ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_Worldpay/payment/ideal',
            isValidated: ko.observable(false),
            shopperBankCode: ko.observable()
        },

        validate: function () {
            this.isValidated(true);

            return this._super() && this.shopperBankCode();
        },

        placeOrder: function (data, event) {
            var i = document.createElement('input');

            i.setAttribute('type', 'hidden');
            i.setAttribute('id', 'wp-shopperbank-code');
            i.setAttribute('data-worldpay-apm', 'shopperBankCode');
            i.setAttribute('value', this.shopperBankCode());

            return this._super(data, event, i);
        }
    });
});
