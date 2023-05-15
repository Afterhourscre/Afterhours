/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

require([
        'jquery'
    ],
    function ($) {
        $("#faq_general_groups_with_disabled_faq,#faq_helpfulness_helpfulness_customer_groups").change(function () {
            if ($(this).val().indexOf('32000') != -1) {
                $(this).val(['32000'])
            }
        });
    }
);