/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column',
    'mage/apply/main',
    'underscore'
], function (Column, mage, _) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_OnSale/ui/grid/columns/cells/label/view',
            positionClassesMap: {},
            positionFieldName: 'position',
            delayMs: 200
        },

        /**
         * Retrieve label position css class
         *
         * @param {Object} record
         * @returns {Object}
         */
        getLabelPositionClass: function (record) {
            var wrapClasses = {},
                positionValue = record[this.positionFieldName];

            if (this.positionClassesMap[positionValue]) {
                wrapClasses[this.positionClassesMap[positionValue]] = true;
            }

            return wrapClasses;
        },

        /**
         * Delay initialization of UI component in column
         *    until column is rendered
         */
        onElementRender: function () {
            _.delay(function() {
                    mage.apply();
                },
                this.delayMs
            );
        }
    });
});
