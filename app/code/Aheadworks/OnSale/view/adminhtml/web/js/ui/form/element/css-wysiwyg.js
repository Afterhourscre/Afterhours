/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'underscore',
    'mageUtils',
    'uiLayout',
    'Aheadworks_OnSale/js/ui/components/label/utils',
    'Aheadworks_OnSale/js/ui/form/element/css-wysiwyg/storage',
    'Magento_Ui/js/form/element/textarea',
    'mage/translate'
], function (_, utils, layout, labelUtils, storage, Textarea, $t) {
    'use strict';

    return Textarea.extend({
        defaults: {
            elementTmpl: 'Aheadworks_OnSale/ui/form/element/css-wysiwyg',
            advancedSettingsTooltipTpl: 'Aheadworks_OnSale/ui/form/element/helper/tooltip/for-advanced-settings',
            collapsibleTitle: $t('Advanced Settings / CSS'),
            previousLabelType: '',
            toolbarElems: {},
            cssProperties: {},
            toolbarConfig: {
                name: '${ $.name }_toolbar',
                component: 'uiComponent',
                children: {}
            },
            storageConfig: {
                name: '${ $.name }_storage',
                component: 'Aheadworks_OnSale/js/ui/form/element/css-wysiwyg/storage'
            },
            imports: {
                addToolbarElems: '${ $.toolbarConfig.name }:elems',
                labelType: 'aw_onsale_label_form.aw_onsale_label_form.general_information.type:value'
            },
            listens: {
                value: 'updateCssProperties updateValueInToolbar',
                labelType: 'updateValueFromStorage'
            },
            modules: {
                toolbar: '${ $.toolbarConfig.name }'
            }
        },

        /**
         * Initializes component
         *
         * @returns {CssWysiwyg} Chainable
         */
        initialize: function () {
            this._super()
                .initToolbar();

            return this;
        },

        /**
         * Update value from storage
         */
        updateValueFromStorage: function () {
            var key = this.index + '.' + this.labelType,
                previousKey = this.index + '.' + this.previousLabelType;

            if (!this.previousLabelType) {
                storage.set(key, this.value())
            } else if (this.previousLabelType) {
                storage.set(previousKey, this.value())
            }

            this.value(storage.get(key));
            this.previousLabelType = this.labelType;
        },

        /**
         * Initializes toolbar
         *
         * @returns {CssWysiwyg} Chainable
         */
        initToolbar: function () {
            var labelConfig = this.buildToolbar();

            layout([labelConfig]);

            return this;
        },

        /**
         * Build toolbar component
         *
         * @returns {Object}
         */
        buildToolbar: function () {
            var toolbarConfig = {};

            toolbarConfig = utils.extend({}, toolbarConfig, this.toolbarConfig);

            return toolbarConfig;
        },

        /**
         * Add toolbar elements
         *
         * @param {Array} toolbarElems
         * @returns {CssWysiwyg} Chainable
         */
        addToolbarElems: function (toolbarElems) {
            _.each(toolbarElems, function (elem) {
                if (elem.css) {
                    this.toolbarElems[elem.css.property] = elem;

                    elem.on('value', this.toolbarValueUpdated.bind(this, elem));

                    this._updateElemValueInToolbar(elem);
                } else if (_.isArray(elem.elems())) {
                    elem.on('elems', this.addChildToolbarElems.bind(this, elem));
                    
                    this.addChildToolbarElems(elem);
                }
            }, this);

            return this;
        },

        /**
         * Add child toolbar elements
         *
         * @param {Object} elem
         * @returns {CssWysiwyg} Chainable
         */
        addChildToolbarElems: function (elem) {
            var elems = elem.elems();

            if (elem.initChildCount === elems.length) {
                this.addToolbarElems(elems);
            }

            return this;
        },

        /**
         * Update value in toolbar elements
         */
        updateValueInToolbar: function () {
            _.each(this.toolbarElems, function (elem) {
                this._updateElemValueInToolbar(elem);
            }, this);
        },

        /**
         * Update cssProperties object after update value in current element
         */
        updateCssProperties: function () {
            this.cssProperties = labelUtils.cssPropertyToObject(this.value());
        },

        /**
         * Handler function which is supposed to be invoked when
         * toolbar element has been updated
         *
         * @param {Object} elem
         * @param {String} value
         * @returns {CssWysiwyg} Chainable
         */
        toolbarValueUpdated: function (elem, value) {
            var elemCssConfig = elem.css,
                cssPropertyValue = this._getPropertyValueByName(elemCssConfig);

            if (_.isEmpty(value)) {
                this._deletePropertyByName(elemCssConfig.property);
            } else if (cssPropertyValue != value) {
                this.setPropertyValueByName(elemCssConfig, value);
            }

            return this;
        },

        /**
         * Update elem value in toolbar
         *
         * @param {Object} elem
         * @private
         */
        _updateElemValueInToolbar: function (elem) {
            var elemCssConfig = elem.css,
                cssPropertyValue = this._getPropertyValueByName(elemCssConfig);

            if (elem.value() != cssPropertyValue) {
                elem.value(cssPropertyValue);
            }
        },

        /**
         * Retrieve css property by name
         *
         * @param {Object} elemCssConfig
         * @returns {String}
         * @private
         */
        _getPropertyValueByName: function (elemCssConfig) {
            return this._prepareElemValue(elemCssConfig, this.cssProperties[elemCssConfig.property], true);
        },

        /**
         * Set css property value by name
         *
         * @param {Object} elemCssConfig
         * @param {String} value
         * @returns {CssWysiwyg} Chainable
         */
        setPropertyValueByName: function (elemCssConfig, value) {
            this.cssProperties[elemCssConfig.property] = this._prepareElemValue(elemCssConfig, value, false);
            this.value(labelUtils.cssPropertyToString(this.cssProperties));

            return this;
        },

        /**
         * Delete css property by name
         *
         * @param {String} name
         * @returns {CssWysiwyg} Chainable
         * @private
         */
        _deletePropertyByName: function (name) {
            delete(this.cssProperties[name]);
            this.value(labelUtils.cssPropertyToString(this.cssProperties));

            return this;
        },

        /**
         * Prepare elem value
         *
         * @param {Object} elemCssConfig
         * @param {String} value
         * @param {Boolean} isRemoveUnit
         * @returns {String}
         * @private
         */
        _prepareElemValue: function (elemCssConfig, value, isRemoveUnit) {
            if (value && elemCssConfig.unit) {
                if (isRemoveUnit) {
                    value = value.replace(elemCssConfig.unit, '');
                } else {
                    value += elemCssConfig.unit;
                }
            }

            return value;
        }
    });
});
