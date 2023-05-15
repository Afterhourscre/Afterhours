/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
define([
    'Magento_Ui/js/form/components/insert-form'
], function (Insert) {
    'use strict';

    return Insert.extend({
        renderForm: function () {
            if (this.isRendered) {
                this.updateData();
            } else {
                return this.render();
            }
        },

        onUpdate: function (data) {
            data = (Array.isArray(data) && data.length == 0) ? {} : data;
            this.externalSource().set('data', data);
            this.externalSource().trigger('data.overload');
            this.externalSource().trigger('data.reset');
            this.loading(false);
        }
    });
});
