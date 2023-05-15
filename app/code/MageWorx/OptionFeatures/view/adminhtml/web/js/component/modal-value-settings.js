/**
 * Copyright © MageWorx. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'jquery',
    'underscore',
    'Magento_Ui/js/modal/modal-component'
], function (registry, $, _, ModalComponent) {
    'use strict';

    return ModalComponent.extend({

        defaults: {
            formName: '',
            buttonName: '',
            isSchedule: false,
            entityProvider: '',
            entityDataScope: '',
            pathModal: 'value_settings_modal.content.fieldset'
        },

        /**
         * Reload modal
         *
         * @param params
         */
        reloadModal: function (params) {
            this.initVariables(params);
            this.initFields();
        },

        /**
         * Initialize variables
         *
         * @param params
         */
        initVariables: function (params) {
            this.entityProvider = params.provider;
            this.entityDataScope = params.dataScope;
            this.buttonName = params.buttonName;
            this.isSchedule = params.isSchedule;
            this.isWeightEnabled = params.isWeightEnabled;
            this.isCostEnabled = params.isCostEnabled;
            if (this.entityProvider === 'catalogstaging_update_form.catalogstaging_update_form_data_source') {
                this.isSchedule = true;
            }
            this.formName = params.formName;
            this.isValidSku = registry.get(this.entityProvider).get(this.entityDataScope).sku_is_valid === '1';
            this.linkedFields = !_.isUndefined(registry.get(this.entityProvider).get('data.product.option_link_fields'))
                ? registry.get(this.entityProvider).get('data.product.option_link_fields')
                : {};
        },

        /**
         * Initialize fields
         */
        initFields: function () {
            if (this.isCostEnabled) {
                var cost = registry
                    .get(this.entityProvider)
                    .get(this.entityDataScope).cost;
                this.costField = registry.get(
                    this.formName + '.' + this.formName + '.' + this.pathModal + '.cost'
                );
                this.costField.value(cost);
                if (!_.isUndefined(this.linkedFields.cost)) {
                    this.costField.disabled(this.isValidSku);
                }
            }

            if (this.isWeightEnabled) {
                var weight = registry
                    .get(this.entityProvider)
                    .get(this.entityDataScope).weight;
                this.weightField = registry.get(
                    this.formName + '.' + this.formName + '.' + this.pathModal + '.weight'
                );
                this.weightField.value(weight);
                if (!_.isUndefined(this.linkedFields.weight)) {
                    this.weightField.disabled(this.isValidSku);
                }

                var weightType = registry
                    .get(this.entityProvider)
                    .get(this.entityDataScope).weight_type;
                weightType = weightType ? weightType : 'fixed';
                this.weightTypeField = registry.get(
                    this.formName + '.' + this.formName + '.' + this.pathModal + '.weight_type'
                );
                this.weightTypeField.value(weightType);
                if (!_.isUndefined(this.linkedFields.weight)) {
                    this.weightTypeField.disabled(this.isValidSku);
                }
            }
        },

        /**
         * save and close modal
         */
        save: function () {
            this.saveData();
            this.toggleModal();
        },

        /**
         * Save data before close modal, update button status
         */
        saveData: function () {
            var cost = 0;
            if (this.isCostEnabled) {
                cost = this.costField.value();
                registry
                    .get(this.entityProvider)
                    .set(this.entityDataScope + '.cost', cost);
            }

            var weight = 0;
            var weightType = 0;
            if (this.isWeightEnabled) {
                weight = this.weightField.value();
                registry
                    .get(this.entityProvider)
                    .set(this.entityDataScope + '.weight', weight);

                weightType = this.weightTypeField.value();
                registry
                    .get(this.entityProvider)
                    .set(this.entityDataScope + '.weight_type', weightType);
            }

            this.updateButtonStatus(cost, weight);
        },

        /**
         * Update button status
         *
         * @param cost
         * @param weight
         */
        updateButtonStatus: function (cost, weight) {
            if ((cost && cost > 0) || (weight && weight > 0)) {
                $('*[data-name="' + this.buttonName + '"]').addClass('active');
            } else {
                $('*[data-name="' + this.buttonName + '"]').removeClass('active');
            }
        }
    });
});
