/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/ui-select'
], function (UiSelect) {
    'use strict';

    return UiSelect.extend({
        /**
         * Reinitialize value
         */
        reinit: function () {
            var val = this.value();

            this.value(null);
            this.value(val);
        }
    });
});
