/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('awhelpdesk.awHelpdeskQuickResponseManager', {
        options: {
            quickResponsesArray: '',
            targetElementId: ''
        },

        /**
         * @inheritDoc
         */
        _create: function () {
            var selectId = '#' + this.element.attr('id');
            this.addSelectedQuickResponse(this.options, selectId);
        },

        /**
         * Add selected quick response to target element
         *
         * @param options
         * @param selectId
         */
        addSelectedQuickResponse: function(options, selectId) {
            var selectElement = $(selectId),
                selectedOption,
                selectedOptionValue,
                quickResponseArrayValues,
                targetElementId = options.targetElementId,
                targetElement =  $(targetElementId),
                targetElementValue,
                targetElementCursorPosition,
                targetElementTextBeforeInsert,
                targetElementTextAfterInsert,
                resultText;

            quickResponseArrayValues = JSON.parse(options.quickResponsesArray);

            selectElement.on('change', function () {
                selectedOption = $(selectId + " option:selected" ),
                selectedOptionValue = selectedOption.val();
                targetElementValue =  targetElement.val();
                targetElementCursorPosition = targetElement.prop('selectionStart');
                targetElementTextBeforeInsert = targetElementValue.substring(0,  targetElementCursorPosition);
                targetElementTextAfterInsert  = targetElementValue.substring(targetElementCursorPosition, targetElementValue.length);
                quickResponseArrayValues.forEach(function (quickResponse) {
                    if (quickResponse.id == selectedOptionValue) {
                        resultText =  targetElementTextBeforeInsert + quickResponse.value + targetElementTextAfterInsert;
                        targetElement.val(resultText);
                        targetElement.focus();
                        selectElement.val(0);
                    }
                })
            });
        }
    });

    return $.awhelpdesk.awHelpdeskQuickResponseManager;
});