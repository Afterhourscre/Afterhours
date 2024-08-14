/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_Coupongenerator/ui/grid/rule/cells/name'
        },
        getName: function(row) {
            return row[this.index];
        },
        getNameUrl: function(row) {
            return row[this.index + '_url'];
        }
    });
});
