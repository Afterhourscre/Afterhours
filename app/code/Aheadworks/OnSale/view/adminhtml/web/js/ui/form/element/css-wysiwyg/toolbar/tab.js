/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'uiComponent'
], function (UiComponent) {
    'use strict';

    return UiComponent.extend({
        defaults: {
            template: 'Aheadworks_OnSale/ui/form/element/css-wysiwyg/toolbar/tab',
            visible: true
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Tab} Chainable
         */
        initObservable: function () {
            this._super()
                .observe('visible');

            return this;
        },

        /**
         * Show element
         *
         * @returns {Tab} Chainable
         */
        show: function () {
            this.visible(true);

            return this;
        },

        /**
         * Hide element
         *
         * @returns {Tab} Chainable
         */
        hide: function () {
            this.visible(false);

            return this;
        }
    });
});
