/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/file-uploader'
], function (Element) {

    return Element.extend({
        defaults: {
            modules: {
                cssContainer: '${ $.ns }.${ $.ns }.general_information.preview_container.customize_css_container'
            }
        },

        /**
         * {@inheritdoc}
         */
        addFile: function (file) {
            var fileInfo;

            this._super(file);

            if (!this.isMultipleFiles && this.cssContainer()) {
                fileInfo = this.value()[0];
                this.cssContainer().setPropertyValueByName({property: 'width', unit: 'px'}, fileInfo.cssWidth);
                this.cssContainer().setPropertyValueByName({property: 'height', unit: 'px'}, fileInfo.cssHeight);
            }

            return this;
        }
    });
});
