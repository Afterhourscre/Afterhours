define([
    'jquery',
    'loader',
    'Mirasvit_Review/js/lib/swiper',
    'domReady!'
], function ($, loader) {
    'use strict';

    $.widget('mst.reviewGallery', {
        displayReviewId: null,
        loadedReviews: {},

        _create: function () {
            const swiper = new Swiper(
                this.options.selector,
                {
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    breakpoints: {
                        "@0.00": {
                            slidesPerView: 4,
                            spaceBetween: 10,
                        },
                        "@0.75": {
                            slidesPerView: 8,
                            spaceBetween: 10,
                        },
                        "@1.00": {
                            slidesPerView: 10,
                            spaceBetween: 10,
                        },
                        "@1.50": {
                            slidesPerView: 14,
                            spaceBetween: 10,
                        },
                    },
                    on: {
                        resize: function () {
                            document.querySelectorAll(this.options.selector + ' .swiper-slide').forEach(function (slide) {
                                slide.style.height = slide.style.width;
                            });
                            this.getSliderWrapper().removeClass('image-hidden'); // do not display images until they resized properly
                            $('.loading-mask', this.getSliderWrapper()).hide();
                        }.bind(this),
                        afterInit: function () {
                            this.getSliderWrapper().removeClass('loading');
                        }.bind(this)
                    }
                }
            );

            $('img', this.element).on('click', this.openGalleryWidget.bind(this));
        },

        openGalleryWidget: function (e) {
            $('body').addClass('review-gallery-active');
            const initialSlide = $(e.target.closest('.swiper-slide'));
            const initialSlideId = initialSlide.attr('aria-label').split('/')[0].trim();

            this.displayReviewId = initialSlide.attr('data-review-id');

            let idsToRequest = [this.displayReviewId];

            this.requestReviewContent(this.getIdsToRequest(initialSlide, idsToRequest));

            const galleryWidget = this.getGalleryWidgetContainer();

            if (galleryWidget.length) {
                galleryWidget.remove();
            }

            this.createGalleryWidget();

            const slides = $('.swiper-slide', this.element).clone();
            slides.removeClass();
            slides.addClass('swiper-slide');
            slides.attr('style', '');

            $('.swiper-wrapper', this.getGalleryWidgetContainer()).append(slides);

            this.renderReviewContent(this.displayReviewId);

            const widgetGallery = new Swiper(
                '#review-gallery-widget',
                {
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    keyboard: {
                        enabled: true,
                    },
                    initialSlide: initialSlideId ? initialSlideId - 1 : 0,
                    on: {
                        slideChangeTransitionStart: this.updateReviewContent.bind(this),
                        keyPress: function (e, keyCode) {
                            if (keyCode == 27) {
                                $('body').removeClass('review-gallery-active');
                                this.getGalleryWidgetContainer().remove();
                            }
                        }.bind(this)
                    }
                }
            );
        },

        getGalleryWidgetContainer: function () {
            return $('.review-gallery-widget-wrapper');
        },

        createGalleryWidget: function () {
            const galleryWidget = $('<div/>');
            galleryWidget.addClass('review-gallery-widget-wrapper');

            galleryWidget.html(this.options.widgetTemplate);

            $('body').append(galleryWidget);

            $('.review-gallery-content', galleryWidget).loader({
                icon: this.options.loaderUrl
            });

            $('.review-gallery-content', galleryWidget).loader('show');

            $('.close-button', galleryWidget).on('click', function () {
                $('body').removeClass('review-gallery-active');
                galleryWidget.remove();
            }.bind(this));
        },

        updateReviewContent: function () {
            const activeSlide = $('.swiper-slide-active', this.getGalleryWidgetContainer());
            const activeSlideReviewId = activeSlide.attr('data-review-id');

            this.requestReviewContent(this.getIdsToRequest(activeSlide, []));

            if (activeSlideReviewId == this.displayReviewId) {
                return;
            }

            this.displayReviewId = activeSlideReviewId;

            this.renderReviewContent(this.displayReviewId);
        },

        getIdsToRequest: function (slide, ids) {
            if (slide.prev('.swiper-slide').length) {
                ids.push(slide.prev('.swiper-slide').attr('data-review-id'));
            }

            if (slide.next('.swiper-slide').length) {
                ids.push(slide.next('.swiper-slide').attr('data-review-id'));
            }

            return [...new Set(ids)];
        },

        requestReviewContent: function (reviewIds) {
            let idsToRequest = reviewIds.filter(function (id) {
                return !this.loadedReviews[id];
            }.bind(this));

            if (idsToRequest.length) {
                $.ajax(this.options.requestUrl + '?review_ids=' + idsToRequest.join(',')).done(function (result) {
                    if (result.success) {
                        for(let idx in result.data) {
                            this.loadedReviews[idx] = result.data[idx];
                        }
                    } else {
                        console.error(result.message)
                    }
                }.bind(this));
            }
        },

        renderReviewContent: function (reviewId) {
            if (!this.loadedReviews[reviewId]) {
                setTimeout(function () {
                    this.renderReviewContent(reviewId);
                }.bind(this), 200)
            } else {
                $('.title', this.getGalleryWidgetContainer()).text(this.loadedReviews[reviewId].title);
                $('.review-gallery-content', this.getGalleryWidgetContainer()).html(this.loadedReviews[reviewId].content);

                $('.review-gallery-content', this.getGalleryWidgetContainer()).loader('hide');
            }
        },

        getSliderWrapper: function () {
            return $('.swiper-wrapper', $(this.options.selector));
        }
    });

    return $.mst.reviewGallery;
})
