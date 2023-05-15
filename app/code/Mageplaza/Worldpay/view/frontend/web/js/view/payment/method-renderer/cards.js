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
    'Magento_Payment/js/view/payment/cc-form',
    'Mageplaza_Worldpay/js/view/payment/vault-enabler',
    'Mageplaza_Worldpay/js/action/apply-3ds',
    'Magento_Checkout/js/action/set-payment-information',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Customer/js/customer-data',
    'rjsResolver',
    'mage/translate',
    'mage/dataPost',
    'worldpayLib'
], function (
    ko,
    $,
    Component,
    VaultEnabler,
    apply3dsAction,
    setPaymentInformationAction,
    additionalValidators,
    fullScreenLoader,
    customerData,
    resolver,
    $t
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageplaza_Worldpay/payment/cards',
            errorValidationMessage: ko.observable(''),
            checked3ds: ko.observable(false),
            active: ko.observable(false),
            isValidated: ko.observable(false),
            cardHolderName: ko.observable(),
            token: null
        },

        /**
         * @returns {exports.initialize}
         */
        initialize: function () {
            this._super();

            this.vaultEnabler = new VaultEnabler();
            this.vaultEnabler.setPaymentCode(this.getVaultCode());

            resolver(this.initWorldpayForm.bind(this));

            return this;
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
                'paymentSection': this.getCode() + '-container',
                'display': 'inline',
                'reusable': this.isVaultEnabled(),
                'saveButton': false,
                'templateOptions': {
                    'images': {enabled: false},
                    'dimensions': {
                        width: 370,
                        height: 275
                    }
                },
                'validationError': function () {
                    fullScreenLoader.stopLoader();

                    self.errorValidationMessage($t('Please fill all required fields with valid information.'));

                    $('body, html').animate({scrollTop: $('#' + self.getCode()).offset().top}, 'slow');
                },
                'callback': function (obj) {
                    fullScreenLoader.stopLoader();
                    if (obj && obj.paymentMethod) {
                        if (!self.getConfig('ccTypes').hasOwnProperty(obj.paymentMethod.cardType)) {
                            self.messageContainer.addErrorMessage({
                                message: $t('Card type "' + obj.paymentMethod.cardType + '" is not allowed')
                            });

                            return;
                        }

                        self.token = obj.token;
                        self.placeOrder();
                    } else {
                        self.messageContainer.addErrorMessage({message: $t('Sorry, but something went wrong')});
                    }
                }
            });
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

        /**
         * @returns {Object}
         */
        getData: function () {
            var data = this._super();

            this.vaultEnabler.visitAdditionalData(data);

            if (!data.hasOwnProperty('additional_data') || !data.additional_data) {
                data.additional_data = {};
            }

            data.additional_data.token     = this.token;
            data.additional_data.cc_holder = this.cardHolderName();

            return data;
        },

        /**
         * @returns {Boolean}
         */
        isVaultEnabled: function () {
            return this.vaultEnabler.isVaultEnabled();
        },

        /**
         * @returns {String}
         */
        getVaultCode: function () {
            return this.getConfig('ccVaultCode');
        },

        isProvided: function () {
            if (this.isIframe()) {
                return true;
            }

            if (this.hasVerification() && !this.creditCardVerificationNumber()) {
                return false;
            }

            return !!(this.cardHolderName() && this.creditCardNumber()
                && this.creditCardExpMonth() && this.creditCardExpYear());
        },

        isActive: function () {
            this.active(this.getCode() === this.isChecked());

            return this.active();
        },

        isIframe: function () {
            return this.getConfig('isIframe');
        },

        validate: function () {
            this.isValidated(true);

            return this._super() && this.isProvided();
        },

        checkout: function (data, event) {
            if (this.isIframe()) {
                this.errorValidationMessage('');
                fullScreenLoader.startLoader();
                Worldpay.submitTemplateForm();
            } else {
                this.placeOrder(data, event);
            }
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
                $('body, html').animate({scrollTop: $('#' + this.getCode()).offset().top}, 'slow');

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
