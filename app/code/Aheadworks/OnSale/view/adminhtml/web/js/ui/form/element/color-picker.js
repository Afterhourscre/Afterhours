/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'awOnSaleColorPicket'
], function ($, _, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            elementTmpl: 'Aheadworks_OnSale/ui/form/element/color-picker',
            colorPickerConfig: {
                allowEmpty:true,
                clickoutFiresChange: true,
                showButtons: false,
                showInitial: true,
                showPalette: true,
                palette: [
                    ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
                    ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
                    ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
                    ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
                    ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
                    ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
                    ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
                    ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
                ]
            },
            listens: {
                value: 'updateValueInColorPicker'
            }
        },
        colorPickerInput: '',

        /**
         * Initializes color picker plugin on provided input element
         *
         * @param {HTMLInputElement} colorPicker
         * @returns {ColorPicker} Chainable
         */
        initColorPicker: function (colorPicker) {
            this.colorPickerInput = colorPicker;

            _.extend(this.colorPickerConfig, {
                move: this.onColorChanged.bind(this)
            });

            $(colorPicker).spectrum(this.colorPickerConfig);
            this.updateValueInColorPicker();

            return this;
        },

        /**
         * Handler function which is supposed to be invoked when
         * color input element has been rendered
         *
         * @param {HTMLInputElement} colorInput
         * @returns {ColorPicker} Chainable
         */
        onElementRender: function (colorInput) {
            this.initColorPicker(colorInput);
            return this;
        },

        /**
         * Handler of the color changed complete event
         *
         * @param {Object} color
         * @returns {ColorPicker} Chainable
         */
        onColorChanged: function (color) {
            var colorValue = color ? color.toHexString() : null;

            this.value(colorValue);
            return this;
        },

        /**
         * Update value in color picker
         */
        updateValueInColorPicker: function () {
            var color = $(this.colorPickerInput).spectrum('get'),
                colorValue = color instanceof tinycolor ? color.toHexString() : null;

            if (this.colorPickerInput && this.value() != colorValue) {
                $(this.colorPickerInput).spectrum('set', this.value());
            }
        }        
    });
});
