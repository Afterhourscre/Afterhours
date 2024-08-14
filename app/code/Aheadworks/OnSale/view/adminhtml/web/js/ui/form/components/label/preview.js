/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mageUtils',
    'Aheadworks_OnSale/js/ui/components/label/utils',
    'uiLayout',
    'uiElement'
], function ($, _, utils, labelUtils, layout, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            template: 'Aheadworks_OnSale/ui/form/components/label/preview',
            labelTmpl: 'Aheadworks_OnSale/ui/form/components/label/preview/label',
            labelConfig: {
                name: '${ $.name }_label',
                component: 'Aheadworks_OnSale/js/ui/components/label',
                imports: {
                    labelType: '${ $.ns }.${ $.ns }.general_information.type:value',
                    shapeType: '${ $.ns }.${ $.ns }.general_information.shape_type:value',
                    pictureInfo: '${ $.ns }.${ $.ns }.general_information.img_file:value'
                }
            },
            areaMap: {},
            sizeList: [],
            positionClassesMap: {},
            imports: {
                labelPosition: '${ $.ns }.${ $.ns }.general_information.position:value'
            },
            listens: {
                labelPosition: 'updateLabelWrapClasses'
            },
            modules: {
                label_large: '${ $.labelConfig.name }_large',
                label_medium: '${ $.labelConfig.name }_medium',
                label_small: '${ $.labelConfig.name }_small'
            }
        },

        /**
         * Initializes component
         *
         * @returns {Preview} Chainable
         */
        initialize: function () {
            this._super()
                .initLabels();

            return this;
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Preview} Chainable
         */
        initObservable: function () {
            this._super()
                .track(['labelPosition'])
                .observe({
                    'labelWrapClasses': {}
                });

            return this;
        },

        /**
         * Initializes label components
         *
         * @returns {Preview} Chainable
         */
        initLabels: function () {
            var labelConfig,
                self = this;

            _.each(this.sizeList, function (size) {
                labelConfig = self.buildLabel(size);
                layout([labelConfig]);
            });

            return this;
        },

        /**
         * Configure label component
         *
         * @param {String} size
         * @returns {Object}
         */
        buildLabel: function (size) {
            var labelConfig = {
                name: this.name + '_label_' + size,
                imports: {
                    text:  this.parentName + '.test_text_' + size + ':value',
                    customizeCssLabel: this.parentName + '.customize_css_label_' + size + ':value',
                    customizeCssContainer: this.parentName + '.customize_css_container_' + size + ':value'
                }
            };

            labelConfig = utils.extend({}, this.labelConfig, labelConfig);

            return labelConfig;
        },

        /**
         * Check if display label for area
         *
         * @param {String} area
         * @return {Boolean}
         */
        isDisplayLabelForArea: function (area) {
            var areaPositions = this.areaMap[area];

            return _.indexOf(areaPositions, this.labelPosition) !== -1;
        },

        /**
         * Update wrapper label classes
         *
         * @returns {Preview} Chainable
         */
        updateLabelWrapClasses: function () {
            var wrapClasses = this.labelWrapClasses();

            wrapClasses = labelUtils.disableAllClasses(wrapClasses);

            if (this.positionClassesMap[this.labelPosition]) {
                wrapClasses[this.positionClassesMap[this.labelPosition]] = true;
                this.labelWrapClasses(wrapClasses);
            }

            return this;
        },

        /**
         * Get label module
         *
         * @returns {Label} Chainable
         */
        retrieveLabelModule: function (size) {
            var modulePath = this.name + '_label_' + size;

            return this.requestModule(modulePath);
        }
    });
});
