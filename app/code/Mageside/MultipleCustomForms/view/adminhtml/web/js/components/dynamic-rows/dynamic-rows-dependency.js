/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
define([
    'underscore',
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'Mageside_MultipleCustomForms/js/components/form/strategy'
], function (_, dynamicRows, strategy) {
    'use strict';

    return dynamicRows.extend(strategy).extend({});
});
