define([
    'jquery',
    "Mirasvit_Review/js/process-reviews"
], function ($, review) {
    'use strict';

    return function (config) {
        var filterBlock = $('#filter_sorting'),
            sorting     = 'sorting=' + $("#sorting").val(),
            direction  = 'direction=desc';

        config.productReviewUrl += '?' + sorting + '&' + direction;

        $('input[name="filter_rating"]').each(function () {
            if ($(this).is(":checked")) {
                config.productReviewUrl += '&rating[]=' + $(this).val();
            }
        });
        $('.mst-review-filter').children('input[type="checkbox"]').each(function () {
            if ($(this).is(":checked")) {
                config.productReviewUrl += '&' + $(this).val() + '=1';
            }

        });

        filterBlock.on('click', 'input[name="filter_rating"]', function () {
            if (!$(this).is(":disabled")) {
                if ($(this).is(":checked")) {
                    config.productReviewUrl +=  '&rating[]=' + $(this).val();
                } else {
                    config.productReviewUrl = config.productReviewUrl.replace('&rating[]=' + $(this).val(), '')
                }

                review(config);
            }
        });

        $('.mst-review-filter').on('click','#verified_buyers', function () {
            if ($(this).is(":checked")) {
                config.productReviewUrl += '&' + $(this).val() + '=1';
            } else {
                config.productReviewUrl = config.productReviewUrl.replace('&' + $(this).val() + '=1', '')
            }

            review(config);

        });

        filterBlock.on('change', 'select', function () {
            if ($(this).is("#sorting")) {
                config.productReviewUrl = config.productReviewUrl.replace(sorting, 'sorting=' + $(this).val());
                sorting = 'sorting=' + $(this).val()
            }

            review(config);

        })

    };
});
