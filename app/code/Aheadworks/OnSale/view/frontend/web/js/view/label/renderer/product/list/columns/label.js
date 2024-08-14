/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    "use strict";

    return Column.extend({

        /**
         * Retrieve onsale label html
         *
         * @param {Object} row
         * @returns {String}
         */
        getLabelHtml: function (row) {
            var image = this.getImage(row);

            if (image.extension_attributes && image.extension_attributes.aw_onsale_label) {
                return image.extension_attributes.aw_onsale_label;
            }

            return '';
        },

        /**
         * Get image data object
         *
         * @returns {Object}
         */
        getImage(row) {
            var imageColumn = this.getImageColumn();

            return _.filter(row.images, function (image) {
                return imageColumn().imageCode === image.code;
            }, this).pop();
        },

        /**
         * Get image module
         *
         * @returns {Function} Async module wrapper
         */
        getImageColumn: function () {
            return this.requestModule(this.parentName + '.image');
        }
    });
});
