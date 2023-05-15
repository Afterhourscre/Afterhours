/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/abstract'
], function (Element) {
    'use strict';

    return Element.extend({
        /**
         * Initializes observable properties of instance
         *
         * @returns {Abstract} Chainable
         */
        initObservable: function () {
            this._super()
                .observe('label');

            return this;
        },

        changeLabel: function (newLabel) {
            this.label(newLabel);
        }
    });
});
