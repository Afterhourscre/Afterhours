/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'mageUtils'
], function($, utils) {
    "use strict";

    $.widget('mage.awOnSaleLabelRenderer', {
        options: {
            moveToSelector: '',
            inParentSelector: '',
            action: 'appendTo'
        },

        /**
         * Initialize widget
         */
        _create: function() {
            var parentElement = this._resolveParentElement(),
                callback = $.proxy(this._init, this);

            if (parentElement instanceof $ && parentElement.length) {
                $.async(this.options.moveToSelector, parentElement, callback);
            } else {
                $.async(this.options.moveToSelector, callback);
            }
        },

        /**
         * Label initialization
         */
        _init: function () {
            var moveToElement = this._resolveMoveToElement();

            if (moveToElement instanceof jQuery && moveToElement.length) {
                this
                    ._move(moveToElement)
                    ._updateMoveToElement(moveToElement)
                    ._updateElement();
            }
        },

        /**
         * Change label position by selector and action type
         *
         * @param {jQuery} moveToElement
         * @returns {mage.awOnSaleLabelRenderer}
         * @private
         */
        _move: function(moveToElement) {
            switch (this.options.action) {
                case 'appendTo':
                    this.element.appendTo(moveToElement);
                    break;
                case 'after':
                    $(moveToElement).after(this.element);
                    break;
            }

            return this;
        },

        /**
         * Update moveToElement
         *
         * @param {jQuery} moveToElement
         * @returns {mage.awOnSaleLabelRenderer}
         */
        _updateMoveToElement: function (moveToElement) {
            moveToElement.css('position', 'relative');

            return this;
        },

        /**
         * Update element
         *
         * @param {jQuery} moveToElement
         * @returns {mage.awOnSaleLabelRenderer}
         */
        _updateElement: function () {
            this.element.show();

            return this;
        },

        /**
         * Resolve moveToElement
         *
         * @returns {jQuery}
         */
        _resolveMoveToElement: function () {
            var parentElement, moveToElement;

            if (this.options.moveToSelector) {
                if (utils.isEmpty(this.options.inParentSelector)) {
                    moveToElement = $(this.options.moveToSelector);
                } else {
                    parentElement = this.element.closest(this.options.inParentSelector);
                    moveToElement = parentElement.find(this.options.moveToSelector);
                }
            }

            return moveToElement;
        },

        /**
         * Resolve parent element
         *
         * @return {undefined}|{jQuery}
         * @private
         */
        _resolveParentElement: function () {
            return !utils.isEmpty(this.options.inParentSelector)
                ? this.element.closest(this.options.inParentSelector)
                : undefined
        }
    });

    return $.mage.awOnSaleLabelRenderer;
});
