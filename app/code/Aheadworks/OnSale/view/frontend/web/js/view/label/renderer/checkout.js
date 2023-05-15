/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'uiElement'
], function (Component) {
    "use strict";

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OnSale/view/label/renderer/checkout'
        },

        /**
         * Retrieve onsale label html
         *
         * @param {Object} item
         * @returns {String}
         */
        getLabelHtml: function (item) {
            if (item.extension_attributes && item.extension_attributes.aw_onsale_label) {
                return item.extension_attributes.aw_onsale_label;
            }

            return '';
        }
    });
});
