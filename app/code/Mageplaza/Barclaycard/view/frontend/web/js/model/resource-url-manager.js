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

define([
    'jquery',
    'Magento_Checkout/js/model/resource-url-manager'
], function ($, resourceUrlManager) {
    'use strict';

    return $.extend(resourceUrlManager, {
        getUrlForGetHostedUrl: function (quote) {
            var params = this.getCheckoutMethod() === 'guest' ? {cartId: quote.getQuoteId()} : {},
                urls   = {
                    'guest': '/guest-carts/:cartId/mpbarclaycard-get-hosted-url',
                    'customer': '/carts/mine/mpbarclaycard-get-hosted-url'
                };

            return this.getUrl(urls, params);
        },

        getUrlForProcess3ds: function (quote) {
            var params = this.getCheckoutMethod() === 'guest' ? {cartId: quote.getQuoteId()} : {},
                urls   = {
                    'guest': '/guest-carts/:cartId/mpbarclaycard-process-3ds',
                    'customer': '/carts/mine/mpbarclaycard-process-3ds'
                };

            return this.getUrl(urls, params);
        }
    });
});
