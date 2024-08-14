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
 * @package     Mageplaza_Worldpay
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/alert'
], function ($, $t, alert) {
    "use strict";

    $.widget('mage.mpworldpayForm', {
        _create: function () {
            var self = this;

            $(this.element).on('change', '.saved_tokens', function () {
                self.prepareCVC($(this).val());
            });
        },

        prepareCVC: function (token) {
            $('#edit_form').off('submitOrder').on('submitOrder', function (event) {
                event.stopPropagation();

                Worldpay.submitTemplateForm();

                return false;
            });

            $('.cvc_container').html('');

            Worldpay.useTemplateForm({
                'clientKey': this.options.clientKey,
                'form': 'co-payment-form',
                'paymentSection': $(this.element).find('.cvc_container'),
                'display': 'inline',
                'type': 'cvc',
                'token': token,
                'saveButton': false,
                'templateOptions': {'dimensions': {width: 230, height: 95}},
                'callback': function (obj) {
                    if (obj && obj.cvc) {
                        order._realSubmit();
                    } else {
                        $('body').trigger('processStop');
                        alert({content: $t('Sorry, but something went wrong')});
                    }
                }
            });
        }
    });

    return $.mage.mpworldpayForm;
});
