define(
    [
        'jquery',
        'uiClass',
        'underscore',
        'uiRegistry'
    ],
    function ($, Class, _, registry) {
    'use strict';

        return Class.extend({
            defaults: {
                modules: {
                    dependencyModal: 'custom_form.custom_form.related_fields.dependency-modal',
                    form: 'custom_form.custom_form',
                    formDependency: 'dependency_input_form.dependency_input_form_data_source'
                }
            },

            /**
             * Initialize actions and adapter.
             *
             * @param {Object} config
             * @param {Element} elem
             * @returns {Object}
             */
            initialize: function (config, elem) {
                return this._super()
                    .initAdapter(elem);
            },

            /**
             * Attach callback handler on button.
             *
             * @param {Element} elem
             */
            initAdapter: function (elem) {
                self = this;
                $(elem).on('click', function (event) {
                    event.preventDefault();
                    var form_dependency = registry.async(self.modules.formDependency);
                    var dependency = form_dependency();
                    var custom_form = registry.async(self.modules.form);
                    var form = custom_form();
                    var emailsPath = form.source.data.form.emails;

                    emailsPath.each(function (element) {
                        if (element.record_id == dependency.data.record_id) {
                            element.dependency = JSON.stringify(dependency.data.fields);
                        }
                    });
                    var modal = registry.async(self.modules.dependencyModal);
                    var closeModal = modal();
                    closeModal.closeModal();

                });

                return this;
            }
        });
});
