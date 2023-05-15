/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/lib/validation/validator',
    'uiComponent',
    'jquery/file-uploader'
], function ($, _, utils, uiAlert, validator, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            value: [],
            maxFileSize: false,
            isMultipleFiles: false,
            allowedExtensions: false,
            previewTmpl: 'ui/form/element/uploader/preview',
            dropZone: '[data-role=drop-zone]',
            isLoading: false,
            countUploadsNotExceed: true,
            fileNamesSerialized: '',
            uploaderConfig: {
                dataType: 'json',
                sequentialUploads: true,
                formData: {
                    'form_key': window.FORM_KEY
                }
            },
            tracks: {
                isLoading: true,
                countUploadsNotExceed: true,
                fileNamesSerialized: true
            },
            listens: {
                value: 'updateCountUploads serializeFileNames'
            },
            error: false
        },

        /** @inheritdoc */
        initConfig: function (config) {
            this.constructor.defaults.isMultipleFiles = config.maxUploads > 1;
            this._super();

            return this;
        },

        initialize: function () {
            this._super();
            validator.addRule('validate-max-number-uploads',
                function (currentCount, maxCount) {
                    return currentCount <= maxCount;
                },
                $.mage.__('You can\'t upload more than ' + this.maxUploads + ' files in one time.')
            );

            return this;
        },

        initUploader: function (fileInput) {
            this.$fileInput = fileInput;

            _.extend(this.uploaderConfig, {
                dropZone:   $(fileInput).closest(this.dropZone),
                change:     this.onFilesChoosed.bind(this),
                drop:       this.onFilesChoosed.bind(this),
                add:        this.onBeforeFileUpload.bind(this),
                done:       this.onFileUploaded.bind(this),
                start:      this.onLoadingStart.bind(this),
                stop:       this.onLoadingStop.bind(this)
            });

            $(fileInput).fileupload(this.uploaderConfig);

            return this;
        },

        initObservable: function () {
            this._super();

            this.observe('error disabled focused preview visible value warn');

            return this;
        },

        removeFile: function (file) {
            this.value.remove(file);

            return this;
        },

        addFile: function (file) {
            file = this.processFile(file);

            this.isMultipleFiles ?
                this.value.push(file) :
                this.value([file]);

            return this;
        },

        updateCountUploads: function (files) {
            this.countUploadsNotExceed = (files.length < parseInt(this.maxUploads));
        },

        serializeFileNames: function (files) {
            var fileNames = [];
            _.each(files, function (file) {
                fileNames.push(file.name);
            });
            this.fileNamesSerialized = fileNames.join();
        },

        processFile: function (file) {
            if (this.checkIfFileImage(file)) {
                this.observe.call(file, true, [
                    'previewWidth',
                    'previewHeight'
                ]);
            }

            return file;
        },

        formatSize: function (bytes) {
            var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'],
                i;
            if (bytes === 0) {
                return '0 Byte';
            }
            i = window.parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));

            return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        },

        getFilePreview: function (file) {
            return file.url;
        },

        checkIfFileImage: function (file) {
            return file.name.match(/.(jpg|jpeg|png|gif|bmp)$/i);
        },

        getPreviewTmpl: function () {
            return this.previewTmpl;
        },

        isFileAllowed: function (file) {
            var result;

            _.every([
                this.isExtensionAllowed(file),
                this.isSizeExceeded(file)
            ], function (value) {
                result = value;

                return value.passed;
            });

            return result;
        },

        isExtensionAllowed: function (file) {
            return validator('validate-file-type', file.name, this.allowedExtensions);
        },

        isSizeExceeded: function (file) {
            return validator('validate-max-size', file.size, this.maxFileSize);
        },

        isCountUploadsExceeded: function (countUploads) {
            return validator('validate-max-number-uploads', countUploads, this.maxUploads);
        },

        notifyError: function (msg) {
            uiAlert({
                content: msg
            });

            return this;
        },

        onFilesChoosed: function (e, data) {
            var countUploads = this.value().length + data.files.length;
            var allowed = this.isCountUploadsExceeded(countUploads);

            if (!allowed.passed) {
                this.notifyError(allowed.message);
                return false;
            }
        },

        onBeforeFileUpload: function (e, data) {
            var file     = data.files[0],
                allowed  = this.isFileAllowed(file);

            if (allowed.passed) {
                $(e.target).fileupload('process', data).done(function () {
                    data.submit();
                });
            } else {
                this.notifyError(allowed.message);
            }
        },

        onFileUploaded: function (e, data) {
            var file    = data.result,
                error   = file.error;

            if (error) {
                this.notifyError(error);
            } else {
                this.addFile(file);
            }
        },

        onLoadingStart: function () {
            this.isLoading = true;
        },

        onLoadingStop: function () {
            this.isLoading = false;
        },

        onElementRender: function (fileInput) {
            this.initUploader(fileInput);
        },

        onPreviewLoad: function (file, e) {
            var img = e.currentTarget;

            file.previewWidth = img.naturalHeight;
            file.previewHeight = img.naturalWidth;
        }
    });
});
