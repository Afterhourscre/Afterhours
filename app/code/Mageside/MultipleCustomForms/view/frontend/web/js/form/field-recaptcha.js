/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
define([
    'jquery',
    'recaptcha'
], function ($) {
    'use strict';

    $.widget('mageside.fieldRecaptcha', {

        options: {
            widgetId: '',
            formId: '',
            widgetPrefix: 'g-recaptcha-',
            inputSelectorPrefix: '#g-recaptcha-response-',
            recaptchaPublicKey: '',
            theme: 'light',
            callbacks: {
                onLoad: 'msOnLoadCallback',
                onVerify: 'msOnVerifyCallback',
                onExpire: 'msOnExpireCallback',
                onReset: 'msOnReset'
            },
            rendered: false,
            widget: null
        },

        _create: function() {
            this._bind();
            this.onRender();
        },

        _bind:function () {
            var self = this;

            $(this.element).parents('form').on('customFormInitFields' + self.options.formId, function (event) {
                self.onRender();
            }.bind(this));

            $('body')
                .on(
                    this.options.callbacks.onLoad,
                    function (event) {
                        self.onRender();
                    }.bind(this)
                )
                .on(
                    this.options.callbacks.onVerify + this.options.widgetId,
                    function (event, response) {
                        self.onVerify(response);
                    }.bind(this)
                )
                .on(
                    this.options.callbacks.onExpire + this.options.widgetId,
                    function (event) {
                        self.onExpire();
                    }.bind(this)
                )
                .on(
                    this.options.callbacks.onReset,
                    function (event, widgetId) {
                        self.onReset(widgetId);
                    }.bind(this)
                );

        },

        onRender: function () {
            if (window.recaptchaLoaded
                && this.options.widget === null
                && window.customFormInited
                && !this.options.rendered
                && this.options.recaptchaPublicKey
            ) {
                this.options.widget = grecaptcha.render(this.options.widgetPrefix + this.options.widgetId, {
                    'sitekey': this.options.recaptchaPublicKey,
                    'callback': this.options.callbacks.onVerify + this.options.widgetId,
                    'expired-callback': this.options.callbacks.onExpire + this.options.widgetId,
                    'theme': this.options.theme
                });
                $(this.element).parents('form').trigger('msRecaptchaReady');
                this.options.rendered = true;
            }
        },

        onVerify: function (response) {
            $(this.options.inputSelectorPrefix + this.options.widgetId).val(response);
            $('body').trigger(this.options.callbacks.onReset, [this.options.widget]);
        },

        onExpire: function () {
            this.reset();
        },

        onReset: function (widgetId) {
            if (this.options.widget !== widgetId) {
                this.reset();
            }
        },

        reset: function () {
            if (this.options.widget !== null) {
                grecaptcha.reset(this.options.widget);
                $(this.options.inputSelectorPrefix + this.options.widgetId).val('');
            }
        }
    });

    return $.mageside.fieldRecaptcha;
});
