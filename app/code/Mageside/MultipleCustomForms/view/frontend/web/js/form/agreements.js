/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'jquery/ui',
    'Magento_Ui/js/modal/modal'
], function($, $t) {
    "use strict";

    $.widget('mageside.agreements', {

        options: {
            agreementSelector: '[data-role="agreement"]',
            buttonSelector: '[data-role="agreement-open"]',
            contentSelector: '[data-role="agreement-content"]',
            modal: {
                title: $t('Agreement terms and conditions.'),
                modalClass: 'agreement'
            }
        },

        _create: function() {
            $(this.options.buttonSelector, this.element.parents(this.options.agreementSelector)).on('click', function (event) {
                event.preventDefault();
                this.showModal($(event.target));
            }.bind(this));
        },

        showModal: function ($agreement) {
            var $modal = $agreement.data('agreement')
                    || $agreement.parents(this.options.agreementSelector).find(this.options.contentSelector);

            if (!$modal.data('mageModal')) {
                this.initModal($modal);
                $agreement.data('agreement', $modal);
            }

            if ($modal.data('mageModal')) {
                $modal.modal('openModal');
            }
        },

        initModal: function ($modal) {
            var self = this,
                $agreementField = this.element;

            $modal.modal({
                title: self.options.modal.title,
                modalClass: self.options.modal.modalClass,
                buttons: [
                    {
                        text: $t('Close'),
                        class: 'action secondary',
                        click: function(event){
                            this.closeModal(event);
                        }
                    },
                    {
                        text: $t('Agree'),
                        class: 'action primary',
                        click: function(event){
                            $agreementField.prop('checked', 'checked');
                            this.closeModal(event);
                        }
                    }
                ]
            });
        }
    });

    return $.mageside.agreements;
});
