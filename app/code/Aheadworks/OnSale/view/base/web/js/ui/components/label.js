/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'ko',
    'underscore',
    'mageUtils',
    'Aheadworks_OnSale/js/ui/components/label/utils',
    'Aheadworks_OnSale/js/ui/components/label/variable/data-processor',
    'uiElement'
], function (ko, _, utils, labelUtils, variableProcessor, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Aheadworks_OnSale/ui/components/label',
            blockInLabelTmpl: {
                'shape-flag-before': 'Aheadworks_OnSale/ui/components/label/shape/flag/before',
                'shape-point_burst-before': 'Aheadworks_OnSale/ui/components/label/shape/point-burst/before'
            },
            shapeConfig: {
                classesMap: {
                    'circle': 'circle',
                    'point_burst': 'point-burst-wrap',
                    'square': 'square',
                    'rectangle': 'rectangle',
                    'rectangle_with_bevel_up': 'rectangle-with-bevel-up',
                    'rectangle_with_bevel_down': 'rectangle-with-bevel-down',
                    'flag': 'flag'
                }
            },
            textConfig: {
                variableValues: {},
                variableProcessors: {
                    'spdl': 'diffInDays',
                    'sphl': 'diffInHours'
                },
                classesMap: {
                    'shape-square': 'absolute-text',
                    'shape-circle': 'absolute-text',
                    'shape-flag': 'absolute-text',
                    'shape-point_burst': 'absolute-text',
                    'picture': 'absolute-text'
                }
            },
            listens: {
                labelType: 'updateLabelClasses updateLabelTextClasses updateBlockTmplInLabel generateStylesheet',
                shapeType: 'updateLabelClasses updateLabelTextClasses updateBlockTmplInLabel',
                pictureInfo: 'generateStylesheet',
                customizeCssLabel: 'generateStylesheet',
                customizeCssContainer: 'generateStylesheet'
            }
        },

        /**
         * Initializes component
         *
         * @returns {Label} Chainable
         */
        initialize: function () {
            this._super()
                .updateLabelClasses()
                .updateLabelTextClasses()
                .updateBlockTmplInLabel()
                .generateStylesheet();

            return this;
        },

        /**
         * Initializes regular properties of instance
         *
         * @returns {Label} Chainable
         */
        initConfig: function () {
            var uid = utils.uniqueid();

            this._super();

            _.extend(this, {
                uid: uid
            });

            return this;
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Label} Chainable
         */
        initObservable: function () {
            this._super()
                .observe([
                    'text',
                    'labelType',
                    'shapeType',
                    'pictureInfo',
                    'customizeCssLabel',
                    'customizeCssContainer'
                ]).observe({
                    'labelClasses': {},
                    'labelTextClasses': {},
                    'customCss': {},
                    'blockTmplInLabel': {'before': false, 'after': false}
                });

            this._text = ko.pureComputed({
                read: function () {
                    return this._processLabelText(this.text());
                },

                owner: this
            });

            return this;
        },

        /**
         * Process label text value
         *
         * @param {String} text
         * @returns {String}
         * @private
         */
        _processLabelText: function (text) {
            var processedVariableValue, variable, callback, regex;

            _.each(this.textConfig.variableValues, function (value, index) {
                processedVariableValue = value;
                variable = index.toLowerCase();
                if (this.textConfig.variableProcessors[variable]) {
                    callback = variableProcessor[this.textConfig.variableProcessors[variable]];
                    processedVariableValue = callback.apply(variableProcessor, [value] || []);
                }
                regex = new RegExp('{' + index + '}', 'g');
                text = text.replace(regex, processedVariableValue);
            }, this);

            return text;
        },

        /**
         * Update label classes
         *
         * @returns {Label} Chainable
         */
        updateLabelClasses: function () {
            var classes = this.labelClasses();

            classes = labelUtils.disableAllClasses(classes);
            switch (this.labelType()) {
                case 'shape':
                    classes[this.shapeConfig.classesMap[this.shapeType()]] = true;
                    break;
            }
            classes[this.labelType()] = true;

            this.labelClasses(classes);
            return this;
        },

        /**
         * Update label text classes
         *
         * @returns {Label} Chainable
         */
        updateLabelTextClasses: function () {
            var classes = this.labelTextClasses(), path = this._getTypePath();

            classes = labelUtils.disableAllClasses(classes);

            if (this.textConfig.classesMap[path]) {
                classes[this.textConfig.classesMap[path]] = true;
            }

            this.labelTextClasses(classes);
            return this;
        },

        /**
         * Update block template value for label
         *
         * @returns {Label} Chainable
         */
        updateBlockTmplInLabel: function () {
            var path, blockTmplInLabel = {'before': false, 'after': false};

            path = this._getTypePath('before');
            if (this.blockInLabelTmpl[path]) {
                blockTmplInLabel['before'] = this.blockInLabelTmpl[path];
            }

            path = this._getTypePath('after');
            if (this.blockInLabelTmpl[path]) {
                blockTmplInLabel['after'] = this.blockInLabelTmpl[path];
            }

            this.blockTmplInLabel(blockTmplInLabel);
            return this;
        },

        /**
         * Retrieve block template in label by position
         *
         * @param {String} position
         * @returns {Boolean|String}
         */
        getBlockTmplInLabel: function (position) {
            return this.blockTmplInLabel()[position];
        },

        /**
         * Retrieve custom css
         *
         * @returns {String}
         */
        getCustomCss: function () {
            var stylesheet = labelUtils.cssPropertyToString(this.customCss());

            return '#' + this.uid + '.' + this.labelType() + ' {' + stylesheet + '}';
        },

        /**
         * Generate global stylesheet
         *
         * @returns {Label} Chainable
         */
        generateStylesheet: function () {
            var stylesheet;

            stylesheet = utils.extend(
                labelUtils.cssPropertyToObject(this.customizeCssLabel()),
                labelUtils.cssPropertyToObject(this.customizeCssContainer()),
                this._generatePictureStylesheet()
            );

            this.customCss(stylesheet);
            return this;
        },

        /**
         * Generate stylesheet for picture
         *
         * @returns {Object}
         */
        _generatePictureStylesheet: function () {
            var stylesheet = {};

            if (this.labelType() === 'picture') {
                if (_.isArray(this.pictureInfo()) && this.pictureInfo().length) {
                    stylesheet['background-image'] = 'url("' + this.pictureInfo()[0].url + '")';
                }
            }
            return stylesheet;
        },

        /**
         * Retrieve label type path
         *
         * @param {String} position
         * @returns {String}
         */
        _getTypePath: function (position) {
            var type, positionArr = position ? [position] : [];

            switch (this.labelType()) {
                case 'shape':
                    type = [this.labelType(), this.shapeType()];
                    break;
                default:
                    type = [this.labelType()];
            }

            return _.union(type, positionArr).join('-');
        }
    });
});
