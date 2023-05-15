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

var config = {
    deps: [
        "Extait_Cookie/js/cookie"
    ],
    map: {
        '*': {
            saveCookieSettings: 'Extait_Cookie/js/settings/save'
        }
    }
};
