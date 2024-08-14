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
 * @package     Mageplaza_CallForPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
define([
    'jquery',
    'validation'
], function ($) {
    "use strict";

    $.widget('callforprice.popupquote', {
        /**
         * _create function index
         * */
        _create: function () {
            var mp_message = $('#mp_message');
            var mpcpf_request_quote_form = $("#mpcpf_request_quote_popup");
            var request_form_url = this.options.request_form_url;

            $("#mp_close_cfp").on('click', function () {
                mp_message.empty();
                $('#mp_popupquote').css('display', "none");
                /** convert template popup when open*/
            });
            mpcpf_request_quote_form.on('submit', function (e) {
                e.preventDefault();
                if (mpcpf_request_quote_form.validation('isValid')) {
                    $.ajax({
                        url: request_form_url,
                        type: 'POST',
                        data: mpcpf_request_quote_form.serialize(),
                        showLoader: true,
                        success: function (result) {
                            if (!result.error) {
                                mpcpf_request_quote_form.trigger('reset');
                                mp_message.removeClass('error').addClass('success');
                            } else {
                                mp_message.removeClass('success').addClass('error');
                            }
                            mp_message.text(result.msg);
                        },
                        error: function (e) {
                            mp_message.text(e.statusText);
                        }
                    });
                }
            });
        }
    });

    return $.callforprice.popupquote;
});
