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
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Mageplaza_Worldpay/js/model/resource-url-manager'
], function (storage, quote, customerData, errorProcessor, fullScreenLoader, resourceUrlManager) {
    'use strict';

    return function (messageContainer, token) {
        fullScreenLoader.startLoader();
        return storage.post(
            resourceUrlManager.getUrlForProcessApm(quote),
            JSON.stringify({token: token})
        ).done(function (response) {
            if (response.message) {
                messageContainer.addErrorMessage({message: response.message});
            } else if (response.redirect_url) {
                customerData.invalidate(['cart', 'checkout-data']);
                window.location.href = response.redirect_url;
            }
        }).fail(function (response) {
            errorProcessor.process(response);
        }).always(function () {
            fullScreenLoader.stopLoader();
        });
    };
});
