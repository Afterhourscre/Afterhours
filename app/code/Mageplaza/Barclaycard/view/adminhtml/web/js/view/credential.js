/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Barclaycard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define(['jquery', 'mage/translate'], function ($, $t) {
    'use strict';

    function showMessage (type, message) {
        $('#mpbarclaycard-messages').html(
            '<div class="message message-' + type + ' ' + type + ' "><span>' + message + '</span></div>'
        );
    }

    function toggleLoader (loading) {
        if (loading) {
            $('#mpbarclaycard-test-button').addClass('disabled');
            $('#mpbarclaycard-spinner').removeClass('no-display');
        } else {
            $('#mpbarclaycard-test-button').removeClass('disabled');
            $('#mpbarclaycard-spinner').addClass('no-display');
        }
    }

    $.widget('mageplaza.barclaycard', {
        _create: function () {
            var self = this;

            $('#mpbarclaycard-test-button').click(function () {
                self.submitToken();
            });
        },

        getWebsiteId: function (url) {
            if (url.includes("website")) {
                var params = url.split("website");
                return params[1].replaceAll("/", "");
            }

            return null;
        },

        submitToken: function () {
            var valid = true,
                path  = '#payment_' + this.options.country + '_mpbarclaycard_credentials_',
                data  = {
                    'psp_id': $(path + 'psp_id').val(),
                    'hash_algorithm': $(path + 'hash_algorithm').val(),
                    'user_id': $(path + 'direct_user_id').val(),
                    'password': $(path + 'direct_password').val(),
                    'sha_in': $(path + 'direct_sha_in').val(),
                    'hosted_user_id': $(path + 'hosted_user_id').val(),
                    'hosted_sha_in': $(path + 'hosted_sha_in').val(),
                    'hosted_sha_out': $(path + 'hosted_sha_out').val(),
                    'websiteId': this.getWebsiteId(location.href) ? this.getWebsiteId(location.href) : "0"
                };

            $.each(data, function (key, value) {
                if (!value) {
                    valid = false;
                }
            });

            if (!valid) {
                showMessage('error', $t('Please fill in all credential fields'));

                return;
            }

            toggleLoader(true);

            $.ajax({
                method: 'POST',
                url: this.options.url,
                data: data,
                complete: function (response) {
                    var type = 'error', message = response.responseText;

                    if (response.responseJSON) {
                        type    = response.responseJSON.type;
                        message = response.responseJSON.message;
                    }

                    showMessage(type, message);
                    toggleLoader(false);
                }
            });
        }
    });

    return $.mageplaza.barclaycard;
});
