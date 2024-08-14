/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'mage/translate',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/modal',
    'jquery/ui',
    'mage/validation',
    'Magento_Ui/js/lib/view/utils/async',
    'Mageside_MultipleCustomForms/js/form/agreements',
    'Mageside_MultipleCustomForms/js/form/field-upload-files',
    'Mageside_MultipleCustomForms/js/form/field-recaptcha'
], function($, _, $t, customerData, modal) {
    "use strict";

    $.widget('mageside.customForm', {

        options: {
            formId: '',
            loaderEnabled: true,
            afterSubmit: 'redirect',
            successMessage: 'Thank you! Your request is sent.',
            formContainerSelector: '.block-custom-form',
            formContentSelector: '.block-content',
            messagesBlockSelector: '.block-messages',
            reCaptcha: {
                selector: '.field.recaptcha',
                show: 'enabled',
                inputSelector: 'g-recaptcha-response',
                disableSubmitButton: false
            },
            display: 'static',
            modalSettings: {
                title: 'Custom form',
                modalClass: 'custom-form-modal',
                buttons: [],
                prefixTriggerName: 'showCustomForm_',
                formCode: null,
                buttonSelector: '#custom-form-open-modal-button'
            },
            modal: null
        },

        _create: function() {
            window.customFormInited = true;
            if (this.options.display !== 'static') {
                this.initModal();
            } else {
                this.element.trigger('customFormInitFields' + this.options.formId);
            }

            this._bind();
            this.prepareFields();
        },

        _bind: function() {
            var self = this;
            this.element.on('submit', function(e) {
                e.preventDefault();
                self.submitForm($(this));
            });
            $(window).on('resize', function() {
                self.resizeReCaptcha();
            });
        },

        isReCaptchaEnabled: function () {
            var customer = customerData.get('customer');
            return this.options.reCaptcha.show === 'enabled'
                || (this.options.reCaptcha.show === 'only_for_guests' && !customer().firstname);
        },

        prepareFields: function () {
            var self = this;
            var $form = $(this.element);
            var $saveButton = $form.find('button.save');
            if (this.isReCaptchaEnabled()) {
                $(this.options.reCaptcha.selector).show();
                if (this.options.reCaptcha.disableSubmitButton) {
                    $saveButton.addClass('disabled');
                    $form.on('msRecaptchaReady', function (event) {
                        $saveButton.removeClass('disabled');
                        self.resizeReCaptcha();
                    });
                }
            }
        },

        validateForm: function () {
            var $form = this.element,
                validationConfig = this.isReCaptchaEnabled()
                    ? {ignore: ":hidden:not(.recaptcha-input, .file-input, input[name^='validate_datetime_'])"}
                    : {ignore: ":hidden:not(.file-input, input[name^='validate_datetime_'])"};
            var formValidator = $form ? $form.data('validator') : null;
            if (!formValidator) {
                $form.validation(
                    _.extend({
                        errorPlacement: function (error, element) {
                            var validationBlock = element.parents('.validation-block');
                            if (validationBlock.length) {
                                validationBlock.siblings(this.errorElement + '.' + this.errorClass).remove();
                                validationBlock.after(error);
                            } else {
                                element.after(error);
                            }
                        }
                    }, validationConfig)
                );
            }

            return $form.validation('isValid');
        },

        submitForm: function (form) {
            var self = this;

            if (!this.validateForm()) {
                return;
            }

            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },

        ajaxSubmit: function(form) {
            var self = this;

            self.element.find(self.options.messagesBlockSelector).html('');

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    if (self.options.loaderEnabled) {
                        $('body').trigger('processStart');
                    }
                },
                success: function(response) {
                    if (self.options.loaderEnabled) {
                        $('body').trigger('processStop');
                    }

                    if (self.options.afterSubmit === 'message' || response.error) {
                        var message = response.messages || self.options.successMessage;
                        self.element
                            .parents(self.options.formContainerSelector)
                            .find(self.options.messagesBlockSelector)
                            .html(message);
                    }

                    if (!response.error) {
                        if (self.options.afterSubmit === 'redirect') {
                            window.location = response.redirectUrl || self.options.redirectUrl || '/';
                        } else {
                            self.element
                                .parents(self.options.formContentSelector)
                                .remove();
                        }
                    }
                }
            });
        },

        initModal: function () {
            var self = this;
            var $customFormModal = self.element.parents(self.options.formContainerSelector);

            this.options.modal = modal(this.options.modalSettings, $customFormModal);

            $(this.options.modalSettings.buttonSelector).on('click', function (event) {
                self.showModal();
            }.bind(this));

            var triggerName = this.options.modalSettings.prefixTriggerName + this.options.modalSettings.formCode;
            $('body').on(triggerName, function (event) {
                self.showModal();
            }.bind(this));
        },

        showModal: function () {
            this.element.trigger('customFormInitFields' + this.options.formId);
            if (this.options.modal) {
                this.options.modal.openModal();
                this.resizeReCaptcha();
            }
        },

        resizeReCaptcha: function () {
            var $recaptcha = this.element.find('.g-recaptcha');
            if ($recaptcha.length) {
                var width = $recaptcha.parents('.custom-form').width();
                var scale = 1;
                if (width < 302) {
                    scale = width / 302;
                }
                $recaptcha.css('transform', 'scale(' + scale + ')');
                $recaptcha.css('-webkit-transform', 'scale(' + scale + ')');
                $recaptcha.css('transform-origin', '0 0');
                $recaptcha.css('-webkit-transform-origin', '0 0');
                $recaptcha.css('width', width);
            }
        }
    });

    return $.mageside.customForm;
});
