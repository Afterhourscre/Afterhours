define(['jquery', 'loader', 'Mirasvit_Review/js/lib/masonry', 'domReady!'], function ($, loader, Masonry) {
    'use strict';
    
    $.widget('mst.reviewGrid', {
        options: {
            gridSelector:  '.review-grid',
            itemSelector:  '.review-grid-item',
            sizerSelector: '.grid-item',
            requestUrl:    '',
            pageSize:      0,
            totalSize:     0,
            loaded:        0,
            sortOrder:     'created_at-desc'
        },
        
        grid: null,
        
        loadMoreButton: null,
        
        _create: function () {
            this.grid = new Masonry(document.querySelector(this.options.gridSelector), {
                itemSelector:       this.options.itemSelector,
                columnWidth:        this.options.sizerSelector,
                percentPosition:    false,
                gutter:             0,
                transitionDuration: 0
            });
            
            $('.reviews-masonry-container').removeClass('hidden');
            
            this.loadMoreButton = $('#load-reviews');
            
            if (this.isFullyLoaded()) {
                this.loadMoreButton.parent().hide()
            }
            
            $('#load-reviews').on('click', this.requestReviews.bind(this))
        },
        
        isFullyLoaded: function () {
            return this.options.totalSize <= this.options.loaded;
        },
        
        requestReviews: function () {
            $('.button-loader', this.loadMoreButton).show()
            $('.button-label', this.loadMoreButton).hide()
            
            let nextPage = Math.ceil(this.options.loaded / this.options.pageSize) + 1;
            
            const params = '?p=' + nextPage + '&limit=' + this.options.pageSize + '&sort_order=' + this.options.sortOrder
            
            let imagesCount = 0;
            
            $.ajax(this.options.requestUrl + params).done(function (result) {
                if (result.success) {
                    result.blocks.forEach(function (block) {
                        let $block = $(block);
                        
                        $block.addClass('hidden')
                        
                        $(this.options.gridSelector).append($block);
                        
                        this.grid.addItems($block)
                        imagesCount += $('img', $block).length;
                    }.bind(this));
                    
                    if (imagesCount) {
                        $('img', this.options.gridSelector).on('load', () => {
                            this.grid.layout(); // update layout after image is loaded
                            $('.review-grid-item', this.options.gridSelector).removeClass('hidden');
                        })
                    } else {
                        this.grid.layout();
                        $('.review-grid-item', this.options.gridSelector).removeClass('hidden');
                    }
                    
                    this.options.loaded += result.loaded
                    
                    if (this.isFullyLoaded()) {
                        this.loadMoreButton.parent().hide()
                    }
                } else {
                    console.error(result.message)
                }
                
                $('.button-loader', this.loadMoreButton).hide()
                $('.button-label', this.loadMoreButton).show()
            }.bind(this))
        }
    });
    
    return $.mst.reviewGrid;
})
