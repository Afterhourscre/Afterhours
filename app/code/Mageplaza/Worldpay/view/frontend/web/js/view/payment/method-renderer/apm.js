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
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Mageplaza_Worldpay/js/action/process-apm',
    'Magento_Checkout/js/action/set-payment-information',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/full-screen-loader',
    'worldpayLib'
], function ($, Component, processApmAction, setPaymentInformationAction, additionalValidators, fullScreenLoader) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_Worldpay/payment/apm'
        },

        /**
         * @returns {String}
         */
        getCode: function () {
            return this.index;
        },

        isActive: function () {
            return this.getCode() === this.isChecked();
        },

        getConfig: function (key) {
            if (window.checkoutConfig.payment[this.getCode()].hasOwnProperty(key)) {
                return window.checkoutConfig.payment[this.getCode()][key];
            }

            return null;
        },

        placeOrder: function (data, event, extraInput) {
            var self = this;

            if (event) {
                event.preventDefault();
            }

            if (!this.validate() || !additionalValidators.validate()) {
                $('body, html').animate({scrollTop: $('#' + this.getCode()).offset().top}, 'slow');

                return false;
            }

            fullScreenLoader.startLoader();

            Worldpay.reusable = false;
            Worldpay.setClientKey(self.getConfig('clientKey'));
            Worldpay.apm.createToken(this.createForm(extraInput), function (status, response) {
                if (response.token) {
                    fullScreenLoader.stopLoader();

                    setPaymentInformationAction(self.messageContainer, self.getData()).done(function () {
                        processApmAction(self.messageContainer, response.token);
                    });

                    return;
                }

                if (response.error && response.error.message) {
                    fullScreenLoader.stopLoader();

                    self.messageContainer.addErrorMessage({message: response.error.message});
                }
            });

            return true;
        },

        createForm: function (extraInput) {
            var form = document.createElement('form'), i;

            i = document.createElement('input');
            i.setAttribute('type', 'hidden');
            i.setAttribute('id', 'wp-apm-name');
            i.setAttribute('data-worldpay', 'apm-name');
            i.setAttribute('value', this.getCode().replace('mpworldpay_', ''));
            form.appendChild(i);

            i = document.createElement('input');
            i.setAttribute('type', 'hidden');
            i.setAttribute('id', 'wp-country-code');
            i.setAttribute('data-worldpay', 'country-code');
            i.setAttribute('value', this.getConfig('merchantCountry'));
            form.appendChild(i);

            i = document.createElement('input');
            i.setAttribute('type', 'hidden');
            i.setAttribute('id', 'wp-language-code');
            i.setAttribute('data-worldpay', 'language-code');
            i.setAttribute('value', this.getConfig('languageCode'));
            form.appendChild(i);

            if (extraInput) {
                form.appendChild(extraInput);
            }

            return form;
        }
    });
});
