define([
        "jquery"
    ],
    function($) {
        "use strict";

        $(document).ready(function($){
            $('.product-add-form .field label.label').on('click', function () {
                $(this).toggleClass('active');
                $(this).siblings().slideToggle();
            });

            $('body.catalog-product-view .product-info-main label.label.admin__field-label > span:contains("[title]")').each(function(){
                var parent = $(this).closest('.choice');
                $(this).html($(this).html().split("[title]").join(""));
                var spanText = $(this).text();
                $(parent).children().not(this).remove();
                $(parent).append('<span>' + spanText + '</span>');
                $(parent).removeAttr('class').removeAttr('data-option_type_id').addClass('options-list-title');
            });

            if(window.outerWidth < 680) {
                $('.footer-v1-content div:eq(1) .footer-title, .footer-v1-content div:eq(2) .footer-title').attr('data-mage-init', '{"accordion":{"openedState": "active", "collapsible": true, "active": false, "multipleCollapsible": false}}');
                var footerColumn = $('.footer-v1-content .footer-links');
                $(footerColumn).attr('data-role', 'collapsible');
                $(footerColumn).find(' > *:first-child').wrap('<div data-role="trigger">' + '</div>');
                $(footerColumn).find(' > *:nth-child(2)').attr('data-role', 'content').css({'margin-bottom':'10px', 'padding-bottom':'10px', 'border-bottom':'1px solid #fff'}).find(' > *').css({'text-align':'center','padding':'0'});
            }

            $(".catalog-product-view .product-add-form .product-options-wrapper .fieldset .field:first-child label").addClass('active');
            $(".catalog-product-view .product-add-form .product-options-wrapper label").on('click', function() {
                $(this).parent().siblings().find('label.label.active').siblings('.tooltip,.control').animate({ height: 'toggle', opacity: 'toggle' }, 'slow').focus();
                $(this).parent().siblings().find('label.label.active').siblings('.control').trigger('click');
                $(this).parent().siblings().find('label.label.active').removeClass('active');
                $(this).parent().siblings().find('.tooltip, .control').fadeOut().toggleClass('active');
            });

            document.addEventListener("contextmenu", function(e){
                if (e.target.nodeName === "IMG") {
                    e.preventDefault();
                }
            }, false);
        });
    });