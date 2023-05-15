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
            template: 'Mageplaza_Worldpay/payment/giropay',
            isValidated: ko.observable(false),
            swiftCode: ko.observable()
        },

        validate: function () {
            this.isValidated(true);

            return this._super() && this.swiftCode();
        },

        placeOrder: function (data, event) {
            var i = document.createElement('input');
            i.setAttribute('type', 'hidden');
            i.setAttribute('id', 'wp-swift-code');
            i.setAttribute('data-worldpay-apm', 'swiftCode');
            i.setAttribute('value', this.swiftCode());

            return this._super(data, event, i);
        }
    });
});
