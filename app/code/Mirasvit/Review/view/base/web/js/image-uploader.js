define([
    'jquery',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/lib/validation/validator',
    'Magento_Ui/js/form/element/image-uploader',
    'mage/adminhtml/browser',
], function ($, _, utils, uiAlert, validator, Element, browser) {
    var currentNumFiles = 0;

    return Element.extend({
        //initUploader: function (fileInput) {
        //    this.$fileInput = fileInput;
        //
        //    _.extend(this.uploaderConfig, {
        //        dropZone: $(fileInput).closest(this.dropZone),
        //        change:   this.onFilesChoosed.bind(this),
        //        drop:     this.onFilesChoosed.bind(this),
        //        add:      this.onBeforeFileUpload.bind(this),
        //        fail:     this.onFail.bind(this),
        //        done:     this.onFileUploaded.bind(this),
        //        start:    this.onLoadingStart.bind(this),
        //        stop:     this.onLoadingStop.bind(this)
        //    });
        //
        //    resizeConfiguration = this.resizeConfig;
        //
        //
        //    $(fileInput).fileupload(this.uploaderConfig);
        //    $(fileInput).fileupload('option', {
        //        processQueue: [
        //            {
        //                action:    'loadImage',
        //                fileTypes: /^image\/(jpeg|png)$/
        //            },
        //            resizeConfiguration,
        //            {
        //                action: 'saveImage'
        //            }
        //        ]
        //    })
        //
        //    return this;
        //},

        onFileUploaded: function (event, data) {

            var uploadedFilename = data.files[0].name,
                file             = data.result,
                error            = file.error;

            error ?
                this.aggregateError(uploadedFilename, error) :
                this.addFile(file);

            $('[name="photos"]').val(JSON.stringify(this.value())).trigger('change');
        },

        onFilesChoosed: function (event, data) {
            maxNumberFiles = this.uploaderConfig.maxNumFiles;
            var files = data.files;

            if (files.length > maxNumberFiles || currentNumFiles + files.length > maxNumberFiles) {
                alert("Max files exceeded, You can't upload more!");
                return false;
            }

            currentNumFiles += files.length;
            return this;
        },

        onFail: function (event, data) {
            alert(data.jqXHR.statusText);

            console.error(data.jqXHR.responseText);
            console.error(data.jqXHR.status);
        },

        removeFile: function (file) {
            this.value.remove(file);

            currentNumFiles -= 1;
            $('[name="photos"]').val(JSON.stringify(this.value())).trigger('change');

            return this;
        },

        replaceInputTypeFile: function (fileInput) {
            let fileId = fileInput.id, fileName = fileInput.name,
                spanElement = '<span id=\'' + fileId + '\'></span>';

            $('#' + fileId).closest('.file-uploader-area').attr('upload-area-id', fileName);
            $(fileInput).replaceWith(spanElement);
            $('#' + fileId).closest('.file-uploader-area').find('.file-uploader-button:first').on('click', function () {
                $('#' + fileId).closest('.file-uploader-area').find('.uppy-Dashboard-browse').trigger('click');
            });

            //add click handler on uploader placeholder
            $('#' + fileId).closest('.file-uploader').find('.file-uploader-placeholder:first').on('click', function () {
                $('#' + fileId).closest('.file-uploader-area').find('.uppy-Dashboard-browse').trigger('click');
            });
        },
    });
});
