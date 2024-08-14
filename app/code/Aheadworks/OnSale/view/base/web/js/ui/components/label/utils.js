/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'underscore',
    'mage/utils/strings'
], function (_, stringUtils) {
    'use strict';

    return {

        /**
         * Parse css property from string
         *
         * @param {String} cssString
         * @returns {Object}
         */
        cssPropertyToObject: function (cssString) {
            var stylesheet = {},
                cssString = cssString || '',
                properties = cssString.split(';'),
                propertyArr, index, value;

            _.each(properties, function(property) {
                propertyArr = property.split(':');
                index = (propertyArr[0] || '').trim();
                value = (propertyArr[1] || '').trim();

                if (!stringUtils.isEmpty(index) && !stringUtils.isEmpty(value)) {
                    stylesheet[index] = value;
                }
            });

            return stylesheet;
        },

        /**
         * Parse css property from string
         *
         * @param {Object} cssObject
         * @returns {String}
         */
        cssPropertyToString: function (cssObject) {
            var stylesheet = '';

            _.each(cssObject, function (value, prop) {
                stylesheet += prop + ': ' + value + ';\n';
            });

            return stylesheet;
        },

        /**
         * Disable all classes
         *
         * @param {Object} classes
         * @returns {Object}
         */
        disableAllClasses: function (classes) {
            _.each(classes, function (val, index) {
                classes[index] = false;
            });

            return classes;
        }
    }
});
