/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
define([
    'jquery',
    'Magento_Ui/js/core/app'
], function ($, app) {
    'use strict';

    $.widget('mageside.fieldUploadFiles', {

        options: {
            formSelector: '#custom-form',
            formId: '',
            uploaderConfig: {},
            rendered: false
        },

        _create: function() {
            var self = this;

            $(this.options.formSelector).on('customFormInitFields' + self.options.formId, function (event) {
                self.initFileUploader();
            }.bind(this));

            self.initFileUploader();
        },

        initFileUploader: function () {
            if (window.customFormInited && !this.options.rendered) {
                app(this.options.uploaderConfig, false);
                this.options.rendered = true;
            }
        }
    });

    return $.mageside.fieldUploadFiles;
});
