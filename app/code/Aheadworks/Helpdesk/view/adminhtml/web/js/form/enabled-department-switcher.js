/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/single-checkbox',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function (SingleCheckbox, alert, $t) {
    'use strict';

    return SingleCheckbox.extend({
        defaults: {
            isDefaultDepartment: false,
            hasTicketsAssigned: false,
            alertTitle: $t('Department can not be disabled')
        },

        /**
         * @inheritdoc
         */
        onCheckedChanged: function (newChecked) {
            if (newChecked) {
                this._super(newChecked);
            } else {
                if (this.canDepartmentBeDisabled()) {
                    this._super(newChecked);
                } else {
                    this.checked(true);
                    alert({
                        title: this.alertTitle,
                        content: this.getAlertContent()
                    });
                }
            }
        },

        /**
         * Check if department can be disabled
         *
         * @returns {boolean}
         */
        canDepartmentBeDisabled: function () {
            return (!this.hasTicketsAssigned) && (!this.isDefaultDepartment);
        },

        /**
         * Retrieve content for disabling failure alert
         *
         * @returns {string}
         */
        getAlertContent: function () {
            var alertContent = '';

            if (this.hasTicketsAssigned) {
                alertContent = $t(
                    'You can disable department only if there are no tickets assigned to it.'
                    + ' Please assign such tickets to other department first.'
                );
            }
            if (this.isDefaultDepartment) {
                alertContent = $t('Default department can not be disabled');
            }

            return alertContent;
        }
    });
});
