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
define([
    'jquery',
    'validation',
    'prototype'
], function ($) {
    "use strict";

    $.widget('callforprice.rule', {
        /**
         * _create function index
         * */
        _create: function () {
            this.availableFields = ['name', 'email', 'phone', 'note']; //TODO: Get this from Model;
            this.showFields = this.options.show_fields.split(',');
            this.arrayRequiredFields = this.options.required_fields.split(',');
            this.initObserve();
            this.hideCFPButtonWhenLogged();
        },

        /**
         * init Observe
         * */
        initObserve: function () {
            var self = this;

            var cfpBtn = this.element.find('#product-callforprice-' + this.options.productId);
            cfpBtn.on('click', function (e) {
                e.stopPropagation();
                switch (self.options.action) {
                    case 'redirect_url':
                        window.location.href = self.options.url_redirect_type;
                        break;
                    case 'login_see_price':
                        self.typeLoginToSeePrice();
                        break;
                    case 'popup_quote_form':
                        self.typePopupQuoteRequest();
                        break;
                    default:
                        break;
                }
            });
        },

        /**
         * function hiden call for price button when loged
         * */
        hideCFPButtonWhenLogged: function () {
            if (this.options.action === 'login_see_price' && this.options.customer_loged_in) {
                this.element.find('#product-callforprice-' + this.options.productId).css('display', 'none');
            }
        },

        /**
         * function action type Popup Quote Request
         * */
        typePopupQuoteRequest: function () {
            $('#mp_product_id').val(this.options.productId);
            $('#mp_customergroup_id').val(this.options.customer_group_id);
            $('#mp_popupquote').css('display', "block");

            /**show field*/
            this.hideFieldonPopup();
            /**required field*/
            this.requiredFieldonPopup();

            this.showQuoteAndTermsInforOnTemplate();
        },

        /**
         * function action type Login To See Price
         * */
        typeLoginToSeePrice: function () {
            var socialLinks = $("a[href='#social-login-popup']");

            if (socialLinks.length) {
                socialLinks.first().click();
            } else {
                window.location.href = this.options.loginUrl; //or window.checkout.customerLoginUrl;
            }
        },

        /**
         * function hide field not allow show on popup
         * */
        hideFieldonPopup: function () {
            var self = this;
            $.each(this.availableFields, function (k, fieldName) {
                var divControlField = $("#mp_request_field_" + fieldName);
                if ($.inArray(fieldName, self.showFields) === -1) {
                    divControlField.hide();
                } else {
                    divControlField.show();
                }
            });
        },

        /**
         * function required field on popup
         * */
        requiredFieldonPopup: function () {
            var self = this;
            $.each(this.availableFields, function (k, fieldName) {
                var fieldInput = $("#mp_" + fieldName);
                var divControlField = $("#mp_request_field_" + fieldName);
                if ($.inArray(fieldName, self.arrayRequiredFields) !== -1) {
                    fieldInput.attr('data-validate', '{required:true}');
                    divControlField.addClass('required');
                    if (fieldName === 'email') {
                        fieldInput.attr('data-validate', '{required:true, \'validate-email\':true}');
                    }
                } else {
                    fieldInput.removeAttr('data-validate');
                    divControlField.removeClass('required');
                }

            });
        },

        /**
         * show quote info and term and condition on template popup
         * */
        showQuoteAndTermsInforOnTemplate: function () {
            $('#mp_quote_heading_title').text(this.options.quote_heading);
            $('#mp_quote_description').text(this.options.quote_description);
            $('#mpcfp_toc_label').html(this.options.tac_label);


            var termField = $('#mp_request_field_terms_condition');
            var termCheckbox = $('#mp_terms_condition');
            if (this.options.enable_terms === '0') {
                termField.hide();
            } else {
                termField.show();
                $('#mpcfp_toc_link').attr('href', this.options.tac_url);
                if (this.options.tac_required === "1") {
                    termCheckbox.attr('data-validate', '{required:true}');
                    termField.addClass('required');
                } else {
                    termCheckbox.removeAttr('data-validate');
                    termField.removeClass('required');
                }

                //keep user's choice
                if (termCheckbox.attr('checked')) {
                    return;
                }
                //check by default
                if (this.options.tac_check_default === '1') {
                    termCheckbox.attr('checked', true)
                } else {
                    termCheckbox.attr('checked', false)
                }
            }
        }

    });

    return $.callforprice.rule;
});
