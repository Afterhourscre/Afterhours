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
    'jquery/ui',
    'mage/cookies'
], function ($) {
    'use strict';

    $.widget('extait.saveCookieSettings', {
        options: {},

        _init: function () {
            var timeoutId;
            $(this.element).find('input[type=checkbox]').on('change', function (event) {
                event.preventDefault();
                var self = $(this);

                clearTimeout(timeoutId);
                timeoutId = setTimeout(function () {
                    self.blur();

                    var $form = self.closest('form'),
                        values = $form.serializeArray(),
                        $systemFields = $form.find('input[data-is-system="1"]'),
                        mageCookieSettings = window.extaitCookie.mageCookieSettings,
                        cookieExpires = new Date(new Date().getTime() + mageCookieSettings.cookieLifetime * 1000),
                        categoriesIDs = [];

                    // Add categories IDs from form request.
                    $(values).each(function (index, element) {
                        var $input = $form.find('input[name="' + element.name + '"]'),
                            categoryID = parseInt($input.attr('data-category-id'));

                        categoriesIDs.push(categoryID);
                    });

                    // Add system categories IDs.
                    $systemFields.each(function (index, element) {
                        var categoryID = parseInt($(element).attr('data-category-id'));

                        if ($.inArray(categoryID, categoriesIDs) === -1) {
                            categoriesIDs.push(categoryID);
                        }
                    });

                    $.mage.cookies.set('extait_allowed_categories', JSON.stringify(categoriesIDs), {
                        expires: cookieExpires
                    });

                    $.ajax({
                        url: '/cookie/ajax/getcookies', method: "POST", data: {},
                        success: function (cookies) {
                            // Set allowed cookies.
                            $.mage.cookies.set('extait_allowed_cookies', JSON.stringify(cookies.allowedCookies), {
                                expires: cookieExpires
                            });

                            $.mage.cookies.set(mageCookieSettings.cookieName, JSON.stringify(mageCookieSettings.cookieValue), {
                                expires: cookieExpires
                            });

                            // Clear disallowed cookies.
                            var headDomain = '.' + location.host.split('.').slice(-2).join('.');
                            $(cookies.disallowedCookies).each(function (index, element) {
                                document.cookie = element + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                                document.cookie = element + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; Domain=" + headDomain;
                            });

                            $("html, body").animate({scrollTop: 0}, 'slow');
                            $("#notice-cookie-block").hide();
                        }
                    });
                }, 2000);
            });
        }
    });
});
