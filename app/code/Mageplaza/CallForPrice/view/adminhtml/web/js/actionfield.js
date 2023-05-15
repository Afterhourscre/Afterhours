/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
define([
        'jquery'
    ], function ($) {
        "use strict";

        return function () {
            var rule_action = $('#rule_action'),
                rule_action_field = $("[data-ui-id='callforprice-rules-edit-tab-wheretoshow-fieldset-element-form-field-button-label']"),
                rule_enable_terms = $("[data-ui-id='callforprice-rules-edit-tab-wheretoshow-fieldset-element-form-field-enable-terms']");

            var optionSlectedFist = $("#rule_action option:selected").val();
            if (optionSlectedFist === 'hide_add_to_cart') {
                rule_action_field.hide();
            }

            if (optionSlectedFist !== 'popup_quote_form') {
                rule_enable_terms.hide();
            }
            /** event change option rule action*/
            rule_action.on('change', function () {
                var optionSlected = $("#rule_action option:selected").val();
                if (optionSlected === 'hide_add_to_cart') {
                    rule_action_field.hide();
                } else {
                    rule_action_field.show();
                }

                if (optionSlected === 'popup_quote_form') {
                    rule_enable_terms.show();
                } else {
                    rule_enable_terms.hide();
                }
            });
        };
    }
);
