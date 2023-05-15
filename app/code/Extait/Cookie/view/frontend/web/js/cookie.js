/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the commercial license
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category Extait
 * @package Extait_Cookie
 * @copyright Copyright (c) 2016-2018 Extait, Inc. (http://www.extait.com)
 */

define([
    'Extait_Cookie/js/cookie-manager'
], function (cookieManager) {
    'use strict';

    var cookieSetterOrig = document.__lookupSetter__("cookie");
    var cookieGetterOrig = document.__lookupGetter__("cookie");

    Object.defineProperty(document, "cookie", {
        get: function () {
            return cookieGetterOrig.apply(document);
        },
        set: function () {
            var cookie = arguments[0].split('; ')[0].split('=');
            if (cookieManager === undefined || cookieManager().isAllow(cookie[0]) || cookie[1] === '') {
                return cookieSetterOrig.apply(document, arguments);
            }
        },
        configurable: true
    });
});
