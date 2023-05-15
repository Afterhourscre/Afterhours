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
    'mage/storage',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/error-processor',
    'Mageplaza_Barclaycard/js/model/resource-url-manager'
], function (storage, quote, fullScreenLoader, errorProcessor, resourceUrlManager) {
    'use strict';

    return function (messageContainer, action) {
        fullScreenLoader.startLoader();
        return storage.post(
            action === 'hosted'
                ? resourceUrlManager.getUrlForGetHostedUrl(quote)
                : resourceUrlManager.getUrlForProcess3ds(quote)
        ).fail(function (response) {
            errorProcessor.process(response, messageContainer);
        }).always(function () {
            fullScreenLoader.stopLoader();
        });
    };
});
