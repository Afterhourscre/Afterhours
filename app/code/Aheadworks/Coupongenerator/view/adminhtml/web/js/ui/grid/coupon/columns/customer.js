/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_Coupongenerator/ui/grid/coupon/cells/customer'
        },
        getName: function(row) {
            if(row['customer_name']) {
                return row['customer_name'];
            }else{
                return '';
            }
        },
        getNameUrl: function(row) {
            return row['column_customer_url'];
        },
        getEmail: function(row) {
            return row['column_customer_email'];
        }
    });
});
