/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/components/button',
    'Magento_Ui/js/lib/spinner',
    'Aheadworks_Acr/js/popup'
], function (Button, spinner, popup) {
    'use strict';

    return Button.extend({
        defaults: {
            popupSelector: '#aw-frame',
            templateSelector: '#aw-template'
        },

        /**
         * @inheritdoc
         */
        applyAction: function (action) {
            var emailData = {
                    subject: this.source.get('data.subject'),
                    content: this.source.get('data.content'),
                    store_ids: this.source.get('data.store_ids'),
                },
                self = this;

            spinner.show();
            popup.create(action.url, emailData, self.popupSelector, self.templateSelector);
        },

        /**
         * Hide element
         *
         * @returns {Abstract} Chainable
         */
        hide: function () {
            this.visible(false);

            return this;
        },

        /**
         * Show element
         *
         * @returns {Abstract} Chainable
         */
        show: function () {
            this.visible(true);

            return this;
        }
    });
});