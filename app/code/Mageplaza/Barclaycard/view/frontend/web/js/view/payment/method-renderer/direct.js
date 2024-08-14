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
    'ko',
    'jquery',
    'Magento_Payment/js/view/payment/cc-form',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/action/set-payment-information',
    'Mageplaza_Barclaycard/js/action/process-payment',
    'Magento_Customer/js/customer-data'
], function (ko, $, Component, additionalValidators, setPaymentInformationAction, processPaymentAction, customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_Barclaycard/payment/direct',
            isValidated: ko.observable(false),
            form3ds: ko.observable()
        },

        initialize: function () {
            this._super();

            if (window.checkoutConfig && window.checkoutConfig.hasOwnProperty('oscConfig')) {
                additionalValidators.registerValidator(this);
            }

            return this;
        },

        /**
         * @return {Boolean}
         */
        selectPaymentMethod: function () {
            this.isValidated(false);

            return this._super();
        },

        /**
         * @returns {String}
         */
        getCode: function () {
            return this.index;
        },

        /**
         * @returns {Object}
         */
        getData: function () {
            var data = this._super();

            if (!data.hasOwnProperty('additional_data') || !data.additional_data) {
                data.additional_data = {};
            }

            data.additional_data.is_frontend = 1;

            return data;
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

        isProvided: function () {
            if (this.hasVerification() && !this.creditCardVerificationNumber()) {
                return false;
            }

            return !!(this.creditCardNumber() && this.creditCardExpMonth() && this.creditCardExpYear());
        },

        validate: function () {
            if (!this.isActive()) {
                return true;
            }

            this.isValidated(true);

            return this._super() && this.isProvided();
        },

        checkout: function (data, event) {
            if (event) {
                event.preventDefault();
            }

            if (!this.validate() || !additionalValidators.validate()) {
                $('body, html').animate({scrollTop: $('#' + this.getCode()).offset().top}, 'slow');

                return false;
            }

            if (this.getConfig('use3ds')) {
                return this.process3ds(data, event);
            }

            return this.placeOrder(data, event);
        },

        process3ds: function (data, event) {
            var self = this;

            setPaymentInformationAction(this.messageContainer, this.getData()).done(function () {
                processPaymentAction(self.messageContainer, '3ds').done(function (response) {
                    if (response) {
                        customerData.invalidate(['cart', 'checkout-data']);

                        self.form3ds(response);
                    } else {
                        self.placeOrder(data, event);
                    }
                });
            });

            return true;
        }
    });
});
