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
    'jquery',
    'Magento_Vault/js/view/payment/method-renderer/vault',
    'Magento_Checkout/js/action/set-payment-information',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Mageplaza_Worldpay/js/action/apply-3ds',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Customer/js/customer-data',
    'mage/translate',
    'mage/dataPost'
], function (
    ko,
    $,
    Component,
    setPaymentInformationAction,
    additionalValidators,
    apply3dsAction,
    fullScreenLoader,
    customerData,
    $t
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_Worldpay/payment/vault',
            errorValidationMessage: ko.observable(''),
            checked3ds: ko.observable(false)
        },

        isActive: function () {
            return this.isChecked() && this.isChecked().includes(this.getCode());
        },

        /**
         * @returns {String}
         */
        getToken: function () {
            return this.publicHash;
        },

        /**
         * Get last 4 digits of card
         * @returns {String}
         */
        getMaskedCard: function () {
            return this.details.maskedCC;
        },

        /**
         * Get expiration date
         * @returns {String}
         */
        getExpirationDate: function () {
            return this.details.expirationDate;
        },

        /**
         * Get card type
         * @returns {String}
         */
        getCardType: function () {
            return this.details.type;
        },

        /**
         * Get image url for CVV
         * @returns {String}
         */
        getCvvImageUrl: function () {
            return window.checkoutConfig.payment.ccform.cvvImageUrl[this.methodCode];
        },

        /**
         * Get image for CVV
         * @returns {String}
         */
        getCvvImageHtml: function () {
            return '<img src="' + this.getCvvImageUrl() +
                '" alt="' + $t('Card Verification Number Visual Reference') +
                '" title="' + $t('Card Verification Number Visual Reference') +
                '" />';
        },

        /**
         * @returns {*}
         */
        getData: function () {
            var data = this._super();

            if (!data.hasOwnProperty('additional_data')) {
                data.additional_data = {};
            }

            data.additional_data.token = this.token;

            return data;
        },

        getConfig: function (key) {
            if (window.checkoutConfig.payment[this.methodCode].hasOwnProperty(key)) {
                return window.checkoutConfig.payment[this.methodCode][key];
            }

            return null;
        },

        selectPaymentMethod: function () {
            this.initWorldpayForm();

            return this._super();
        },

        initWorldpayForm: function () {
            var self = this;

            $('#token_container').remove();

            Worldpay.useTemplateForm({
                'clientKey': this.getConfig('clientKey'),
                'form': 'co-payment-form',
                'paymentSection': this.getId() + '-container',
                'display': 'inline',
                'type': 'cvc',
                'token': this.token,
                'saveButton': false,
                'templateOptions': {'dimensions': {width: 230, height: 95}},
                'validationError': function () {
                    fullScreenLoader.stopLoader();

                    self.errorValidationMessage($t('Please fill all required fields with valid information.'));

                    $('body, html').animate({scrollTop: $('#' + self.getId()).offset().top}, 'slow');
                },
                'callback': function (obj) {
                    fullScreenLoader.stopLoader();
                    if (obj && obj.cvc) {
                        self.placeOrder();
                    } else {
                        self.messageContainer.addErrorMessage({message: $t('Sorry, but something went wrong')});
                    }
                }
            });
        },

        checkout: function () {
            this.errorValidationMessage('');
            fullScreenLoader.startLoader();
            Worldpay.submitTemplateForm();
        },

        placeOrder: function (data, event) {
            var self = this;

            if (this.checked3ds()) {
                this.checked3ds(false);
                return this._super(data, event);
            }

            if (event) {
                event.preventDefault();
            }

            if (!this.validate() || !additionalValidators.validate()) {
                return false;
            }

            if (!this.getConfig('use3ds')) {
                return this._super(data, event);
            }

            setPaymentInformationAction(this.messageContainer, this.getData()).done(function () {
                apply3dsAction(self.messageContainer).done(function (response) {
                    if (response.message) {
                        self.checked3ds(false);
                        self.messageContainer.addErrorMessage({message: response.message});
                    } else if (response.redirect_url) {
                        self.process3DSecure(response);
                    } else {
                        self.checked3ds(true);
                        self.placeOrder(data, event);
                    }
                });
            });

            return true;
        },

        process3DSecure: function (response) {
            var urlParams = {
                'action': response.redirect_url,
                'data': {
                    'PaReq': response.pa_req,
                    'TermUrl': this.getConfig('secureUrl'),
                    'MD': '' // merchant data (optional)
                }
            };

            $.mage.dataPost().postData(urlParams);

            customerData.invalidate(['cart', 'checkout-data']);
        }
    });
});
