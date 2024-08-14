/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'Magento_Ui/js/form/components/button',
    'Magento_Ui/js/modal/alert'
], function ($, Element, alert) {
    'use strict';

    return Element.extend({
        defaults: {
            modules: {
                customForm: 'custom_form.custom_form_data_source'
            }
        },

        /**
         * Initializes component.
         *
         * @returns {Object} Chainable.
         */
        initialize: function () {
            return this._super();
        },

        action: function () {
            var form = this.customForm();
            var formId = form.data.form.id;

            if (!formId) {
                alert({
                    title: 'Form save is required',
                    content: 'Please save form before adding fields'
                });
            } else {
                this.actions.forEach(this.applyAction, this);

            }
        }
    });
});
