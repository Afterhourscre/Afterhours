/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
], function () {
    'use strict';

    var privateData = {};

    return {

        /**
         * Retrieve storage data by key
         *
         * @param {String} key
         * @returns {String}
         */
        get: function (key) {
            return privateData[key];
        },

        /**
         * Set data to storage by key
         *
         * @param {String} key
         * @param {String} value
         */
        set: function (key, value) {
            privateData[key] = value;
        },

        /**
         * Remove data from storage by key
         *
         * @param {String} key
         * @returns {String}
         */
        remove: function (key) {
            delete(privateData[key]);
        }
    }
});
