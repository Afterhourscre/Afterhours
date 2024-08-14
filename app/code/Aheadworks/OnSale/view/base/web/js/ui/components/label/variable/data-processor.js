/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'underscore',
    'moment'
], function (_, moment) {
    'use strict';

    return {
        dateFormat: 'YYYY-MM-DD HH:mm:ss',

        /**
         * Difference date in hours
         *
         * @param {String} date
         * @returns {String}
         */
        diffInHours: function (date) {
            return this._diffBetweenDateAndCurrentDate(date, 'hours');
        },

        /**
         * Difference date in days
         *
         * @param {String} date
         * @returns {String}
         */
        diffInDays: function (date) {
            return this._diffBetweenDateAndCurrentDate(date, 'days');
        },

        /**
         * Difference between two dates in type
         *
         * @param {String} date
         * @param {String} diffInType
         * @returns {String}
         * @private
         */
        _diffBetweenDateAndCurrentDate: function (date, diffInType) {
            var result = 0,
                currentDate,
                processedDate = moment(date, this.dateFormat, true);

            switch (diffInType) {
                case 'hours':
                    currentDate = moment().startOf('hour');
                    break;
                case 'days':
                    currentDate = moment().startOf('day');
                    break;
                default:
                    currentDate = moment()
            }

            if (date && processedDate.isValid()) {
                result = processedDate.diff(currentDate, diffInType);
            }

            return result > 0 ? result : '';
        }
    }
});
