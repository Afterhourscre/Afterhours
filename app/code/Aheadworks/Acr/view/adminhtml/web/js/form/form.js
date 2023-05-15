/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/form'
], function (Form) {
    'use strict';

    return Form.extend({
        /**
         * Validate and save form with sendtest parameter.
         *
         * @param {String} redirect
         * @param {Object} data
         */
        sendtest: function (redirect, data) {
            if (typeof data == "undefined") {
                data = {
                    "sendtest": 1,
                }
            } else {
                data['sendtest'] = 1;
            }
            this.save(redirect, data);
        },
    })
});