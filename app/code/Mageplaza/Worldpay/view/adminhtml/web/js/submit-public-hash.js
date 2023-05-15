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

require(['jquery', 'worldpayLib'], function ($) {
    "use strict";

    $(document).ready(function () {
        $('#edit_form').on('submit', function () {
            var publicHash = $('#payment_form_mpworldpay_cards_vault').find('input[type=radio]:checked').attr('id');

            /**
             * Ignore validate public hash is undefined, it will validate when submit order
             */
            $('#mpworldpay_cards_vault_public_hash').val(publicHash);
        });
    });
});
