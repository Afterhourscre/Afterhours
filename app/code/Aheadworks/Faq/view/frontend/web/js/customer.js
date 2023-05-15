/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            isLoggedIn: false
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super();
            var customerInfo = customerData.get('customer');

            if (this.isLoggedIn && (!customerInfo().fullname || !customerInfo().email)) {
                customerData.reload('customer', false);
                customerInfo = customerData.get('customer');
            }
            this.awFaqCustomer = customerInfo;
        }
    });
});