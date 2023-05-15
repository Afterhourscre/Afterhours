
define(
    [
        'jquery',
        'Magento_Ui/js/form/element/abstract',
        'Magento_Ui/js/modal/alert'
    ],
    function ($, Element, alert) {
    'use strict';

    return Element.extend({
        defaults: {
            modules: {
                dependencyModal: 'custom_form.custom_form.related_fields.dependency-modal',
                formDependency: 'custom_form.custom_form.related_fields.dependency-modal.dependency_fields_form'
            }
        },

        initialize: function () {
            this._super();
            return this;
        },

        openModal: function () {
            var modal = this.dependencyModal();
            var recordId = this.parentName.split('.').last();
            var form = this.formDependency();
            var formId = form.source.data.form.id;
            if (formId) {
                var emails = form.source.data.form.emails;
                modal.openModal();
                var fieldId = "";
                emails.each(function (email) {
                    if (recordId == email.record_id) {
                        fieldId = email.id
                    }
                });

                if (form.isRendered) {
                    form.destroyInserted();
                    form.isRendered = false;
                }
                form.render(
                    {
                        record_id:recordId,
                        field_id:fieldId
                    }
                );
            } else {
                alert({
                    title: 'Form save is required',
                    content: 'Please save form before adding dependencies'
                });
            }
        }
    });
});
