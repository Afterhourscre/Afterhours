/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/actions',
    'Magento_Ui/js/lib/spinner',
    'Aheadworks_Acr/js/popup'
], function (Actions, spinner, popup) {
    'use strict';

    return Actions.extend({
        defaults: {
            popupSelector: '#aw-frame',
            templateSelector: '#aw-template'
        },

        /**
         * @inheritdoc
         */
        applyAction: function (actionIndex, rowIndex) {
            var action = this.getAction(rowIndex, actionIndex),
                self = this,
                emailData = {
                    id: action.recordId
                };

            if (actionIndex == 'preview') {
                spinner.show();
                popup.create(action.href, emailData, self.popupSelector, self.templateSelector);
            }
            return this._super(actionIndex, rowIndex);
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
