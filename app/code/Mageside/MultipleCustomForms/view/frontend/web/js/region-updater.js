define([
    'jquery',
    'regionUpdater'
], function ($) {
    'use strict';

    $.widget('mageside.regionUpdater', $.mage.regionUpdater, {
        options: {
            isMultiSelect: false,
            checkRegionRequired: false
        },

        _renderSelectOption: function (selectElement, key, value) {
            var defaultRegions = this.options.defaultRegion ? this.options.defaultRegion.split(",") : false;
            selectElement.append($.proxy(function () {
                var name = value.name.replace(/[!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~]/g, '\\$&'),
                    tmplData,
                    tmpl;

                if (value.code && $(name).is('span')) {
                    key = value.code;
                    value.name = $(name).text();
                }

                tmplData = {
                    value: key,
                    title: value.name,
                    isSelected: false
                };

                if (defaultRegions && $.inArray(key, defaultRegions) >= 0) {
                    tmplData.isSelected = true;
                }

                tmpl = this.regionTmpl({
                    data: tmplData
                });

                return $(tmpl);
            }, this));
        },

        _removeSelectOptions: function (selectElement) {
            var self = this;
            selectElement.find('option').each(function (index) {
                if (self.options.isMultiSelect || index) {
                    $(this).remove();
                }
            });
        },

        /**
         * Original method switches on isRegionRequired to true if country has regions data
         *
         * @param country
         * @private
         */
        _checkRegionRequired: function (country) {
            if (this.options.checkRegionRequired) {
                this._super(country);
            }
        }
    });

    return $.mageside.regionUpdater;
});