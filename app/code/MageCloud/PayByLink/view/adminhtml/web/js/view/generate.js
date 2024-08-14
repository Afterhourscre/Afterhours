define(['jquery', 'mage/translate'], function ($, $t) {
    'use strict';

    function showMessage (type, message) {
        $('#paybylink-messages').html(
            '<div class="message message-' + type + ' ' + type + ' "><span>' + message + '</span></div>'
        );
    }

    function toggleLoader (loading) {
        if (loading) {
            $('#mpbarclaycard-generate-button').addClass('disabled');
            $('#paybylink-spinner').removeClass('no-display');
        } else {
            $('#mpbarclaycard-generate-button').removeClass('disabled');
            $('#paybylink-spinner').addClass('no-display');
        }
    }

    $.widget('magecloud.barclaycard', {
        _create: function () {
            var self = this;

            $('#mpbarclaycard-generate-button').click(function () {
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
            var validCredential = true,
                valid = true,
                path = '#payment_' + this.options.country + '_mpbarclaycard_paybylink_',
                pathCredential  = '#payment_' + this.options.country + '_mpbarclaycard_credentials_',
                data  = {
                    'order_number': $(path + 'order_number').val(),
                    'amount': $(path + 'amount').val(),
                },
                dataCredential  = {
                    'psp_id': $(pathCredential + 'psp_id').val(),
                    'hash_algorithm': $(pathCredential + 'hash_algorithm').val(),
                    'user_id': $(pathCredential + 'direct_user_id').val(),
                    'password': $(pathCredential + 'direct_password').val(),
                    'sha_in': $(pathCredential + 'direct_sha_in').val(),
                    'hosted_user_id': $(pathCredential + 'hosted_user_id').val(),
                    'hosted_sha_in': $(pathCredential + 'hosted_sha_in').val(),
                    'hosted_sha_out': $(pathCredential + 'hosted_sha_out').val(),
                    'websiteId': this.getWebsiteId(location.href) ? this.getWebsiteId(location.href) : "0"
                };

            $.each(data, function (key, value) {
                if (!value) {
                    valid = false;
                }
            });

            if (!valid) {
                showMessage('error', $t('Please fill in all fields'));

                return;
            }

            if (!$.validator.validateSingleElement('#payment_gb_mpbarclaycard_paybylink_amount')) {
                showMessage('error', $t('Amount field not valid'));
                return;
            }


            $.each(dataCredential, function (key, value) {
                if (!value) {
                    validCredential = false;
                }
            });

            if (!validCredential) {
                showMessage('error', $t('Please fill in all credential fields'));

                return;
            }

            toggleLoader(true);

            $.extend(true, data, dataCredential);

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

    return $.magecloud.barclaycard;
});
