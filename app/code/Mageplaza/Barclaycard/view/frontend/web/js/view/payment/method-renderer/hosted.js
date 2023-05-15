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

define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/action/set-payment-information',
    'Mageplaza_Barclaycard/js/action/process-payment',
    'Magento_Customer/js/customer-data',
    'mage/dataPost'
], function ($, Component, additionalValidators, setPaymentInformationAction, processPaymentAction, customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_Barclaycard/payment/hosted'
        },

        /**
         * @returns {String}
         */
        getCode: function () {
            return this.index;
        },

        getConfig: function (key) {
            if (window.checkoutConfig.payment[this.getCode()].hasOwnProperty(key)) {
                return window.checkoutConfig.payment[this.getCode()][key];
            }

            return null;
        },

        isActive: function () {
            return this.getCode() === this.isChecked();
        },

        checkout: function (data, event) {
            var self = this;

            if (event) {
                event.preventDefault();
            }

            if (!this.validate() || !additionalValidators.validate()) {
                return;
            }

            setPaymentInformationAction(this.messageContainer, this.getData()).done(function () {
                processPaymentAction(self.messageContainer, 'hosted').done(function (response) {
                    customerData.invalidate(['cart', 'checkout-data']);

                    $.mage.dataPost().postData(JSON.parse(response));
                });
            });
        }
    });
});
