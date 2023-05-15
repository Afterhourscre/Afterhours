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
    'jquery',
    'uiComponent'
], function ($, Component) {
    'use strict';

    return Component.extend({
        isAllow: function (name) {
            if (window.extaitCookie && window.extaitCookie.isModuleEnable) {
                var allowedCookies = JSON.parse($.cookie('extait_allowed_cookies')),
                    allCookies = window.extaitCookie.allCookiesNames;
                
                if ($.inArray(name, allCookies) === -1) {
                    //new cookie
                    window.extaitCookie.allCookiesNames.push(name);
                    $.ajax({
                        url: window.extaitCookie.addCookieUrl,
                        method: "POST",
                        data: {
                            cookie: name
                        }
                    });
                }

                if (allowedCookies && $.inArray(name, allowedCookies) === -1) {
                    return false
                }
            }

            return true;
        }
    });
});
