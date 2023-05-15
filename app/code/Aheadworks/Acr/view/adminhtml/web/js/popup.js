/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'jquery',
        'Magento_Ui/js/lib/spinner',
        'mage/template',
        'Magento_Ui/js/modal/modal'
    ],
    function ($, spinner, mageTemplate, modal) {
        'use strict';

        return {
            /**
             * Get content
             *
             * @param string url
             * @param array data
             * @param string popupSelector
             */
            create: function (url, data, popupSelector, templateSelector) {
                var options = {
                        autoOpen: true,
                        responsive: true,
                        clickableOverlay: false,
                        innerScroll: true,
                        modalClass: 'email-preview-modal',
                        title: $.mage.__('Email Preview'),
                        buttons: [{
                            text: $.mage.__('Ok'),
                            class: '',
                        }]
                    },
                    template = mageTemplate(templateSelector),
                    html,
                    popupContent;

                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: 'json',
                    data: {
                        email_data: data
                    },
                    success: function(response) {
                        if (response.ajaxExpired) {
                            window.location.href = response.ajaxRedirect;
                        }

                        html = template({
                            data: {
                                url: response.url
                            }
                        });

                        spinner.hide();

                        if (!response.error) {
                            $(popupSelector).remove();
                            popupContent = $(html).hide();
                            $('body').append(popupContent);
                            modal(options, $(popupSelector));
                            return true;
                        }
                        this.onError(response.message);
                        return false;
                    }
                });
            },

            /**
             * Ajax request error handler
             *
             * @param errorMessage
             */
            onError: function (errorMessage) {
                alert({
                    content: $.mage.__(errorMessage),
                });
            }
        };
    }
);
