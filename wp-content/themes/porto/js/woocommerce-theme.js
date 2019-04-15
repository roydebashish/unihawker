// Theme Functions
function portoCalcSliderMargin($parent, padding) {
    $parent.css({
        'margin-left': '-' + padding,
        'margin-right': '-' + padding
    });
}

function portoCalcSliderButtonsPosition($parent, padding) {
    var $buttons = $parent.find('.show-nav-title .owl-nav');
    if ($buttons.length) {
        if (window.theme.rtl) {
            $buttons.css('left', padding);
        } else {
            $buttons.css('right', padding);
        }
        if ($buttons.closest('.porto-products').length && $buttons.closest('.porto-products').parent().children('.products-slider-title').length) {
            var $title = $buttons.closest('.porto-products').parent().children('.products-slider-title'),newMT = $title.offset().top - $parent.offset().top - parseInt($title.css('padding-top'), 10) - parseInt($title.css('line-height'), 10) / 2 + $buttons.children().outerHeight() / 2 - parseInt($buttons.children().css('margin-top'), 10);
            $buttons.css('margin-top', newMT);
        }
    }
}

function portoCalcSliderTitleLine($parent) {
    var c_w = $parent.width();
    var $title = $parent.parent().find('.slider-title');
    if (!$title.length) return;

    var $l = $title.find('.line');
    var $t = $title.find('.inline-title');

    if (!$t.length || !$l.length) return;

    var title_w = $title.width();
    var t_w = $t.width();
    if (title_w > t_w + 200) {
        if (window.theme.rtl) {
            $l.css({
                display: 'block',
                right: t_w + 20,
                width: title_w - t_w - 75
            });
        } else {
            $l.css({
                display: 'block',
                left: t_w + 20,
                width: title_w - t_w - 75
            });
        }
    } else {
        $l.css({
            display: 'none'
        });
    }
}

// Woocommerce Widget Toggle
(function(theme, $) {

    theme = theme || {};

    var instanceName = '__wooWidgetToggle';

    var WooWidgetToggle = function($el, opts) {
        return this.initialize($el, opts);
    };

    WooWidgetToggle.defaults = {

    };

    WooWidgetToggle.prototype = {
        initialize: function($el, opts) {
            if ($el.data(instanceName)) {
                return this;
            }

            this.$el = $el;

            this
                .setData()
                .setOptions(opts)
                .build();

            return this;
        },

        setData: function() {
            this.$el.data(instanceName, this);

            return this;
        },

        setOptions: function(opts) {
            this.options = $.extend(true, {}, WooWidgetToggle.defaults, opts, {
                wrapper: this.$el
            });

            return this;
        },

        build: function() {
            var self = this,
                $el = this.options.wrapper;

            $el.parent().removeClass('closed');
            if (!$el.find('.toggle').length) {
                $el.append('<span class="toggle"></span>');
            }
            $el.find('.toggle').click(function() {
                if ($el.next().is(":visible")){
                    $el.parent().addClass('closed');
                } else {
                    $el.parent().removeClass('closed');
                }
                $el.next().stop().slideToggle(200);
                theme.refreshVCContent();
            });

            return this;
        }
    };

    // expose to scope
    $.extend(theme, {
        WooWidgetToggle: WooWidgetToggle
    });

    // jquery plugin
    $.fn.themeWooWidgetToggle = function(opts) {
        return this.map(function() {
            var $this = $(this);

            if ($this.data(instanceName)) {
                return $this.data(instanceName);
            } else {
                return new theme.WooWidgetToggle($this, opts);
            }

        });
    }

}).apply(this, [window.theme, jQuery]);


// Woocommerce Widget Accordion
(function(theme, $) {

    theme = theme || {};

    var instanceName = '__wooWidgetAccordion';

    var WooWidgetAccordion = function($el, opts) {
        return this.initialize($el, opts);
    };

    WooWidgetAccordion.defaults = {

    };

    WooWidgetAccordion.prototype = {
        initialize: function($el, opts) {
            if ($el.data(instanceName)) {
                return this;
            }

            this.$el = $el;

            this
                .setData()
                .setOptions(opts)
                .build();

            return this;
        },

        setData: function() {
            this.$el.data(instanceName, this);

            return this;
        },

        setOptions: function(opts) {
            this.options = $.extend(true, {}, WooWidgetAccordion.defaults, opts, {
                wrapper: this.$el
            });

            return this;
        },

        build: function() {
            var self = this,
                $el = this.options.wrapper;

            $el.find('ul.children').each(function() {
                var $this = $(this);
                if (!$this.prev().hasClass('toggle')) {
                    $this.before(
                        $('<span class="toggle"></span>').click(function() {
                            var $that = $(this);
                            if ($that.next().is(":visible")) {
                                $that.parent().removeClass('open').addClass('closed');
                            } else {
                                $that.parent().addClass('open').removeClass('closed');
                            }
                            $that.next().stop().slideToggle(200);
                            theme.refreshVCContent();
                        })
                    );
                }
            });
            $el.find('li[class*="current-"]').addClass('current');

            return this;
        }
    };

    // expose to scope
    $.extend(theme, {
        WooWidgetAccordion: WooWidgetAccordion
    });

    // jquery plugin
    $.fn.themeWooWidgetAccordion = function(opts) {
        return this.map(function() {
            var $this = $(this);

            if ($this.data(instanceName)) {
                return $this.data(instanceName);
            } else {
                return new theme.WooWidgetAccordion($this, opts);
            }

        });
    }

}).apply(this, [window.theme, jQuery]);


// Woocommerce Products Slider
(function(theme, $) {

    theme = theme || {};

    var instanceName = '__wooProductsSlider';

    var WooProductsSlider = function($el, opts) {
        return this.initialize($el, opts);
    };

    WooProductsSlider.defaults = {
        rtl: theme.rtl,
        autoplay : theme.slider_autoplay == '1' ? true : false,
        autoplayTimeout: theme.slider_speed ? theme.slider_speed : 5000,
        loop: theme.slider_loop,
        nav: false,
        navText: ["", ""],
        dots: false,
        autoplayHoverPause : true,
        items : 1,
        responsive : {},
        autoHeight : true,
        lazyLoad: true
    };

    WooProductsSlider.prototype = {
        initialize: function($el, opts) {
            if ($el.data(instanceName)) {
                return this;
            }

            this.$el = $el;

            this
                .setData()
                .setOptions(opts)
                .build();

            return this;
        },

        setData: function() {
            this.$el.data(instanceName, this);

            return this;
        },

        setOptions: function(opts) {
            this.options = $.extend(true, {}, WooProductsSlider.defaults, opts, {
                wrapper: this.$el
            });

            return this;
        },

        build: function() {
            var self = this,
                $el = this.options.wrapper,
                lg = this.options.lg,
                md = this.options.md,
                xs = this.options.xs,
                ls = this.options.ls,
                $slider_wrapper = $el.closest('.slider-wrapper'),
                single = this.options.single,
                dots = this.options.dots,
                nav = this.options.nav,
                responsive = {},
                items,
                scrollWidth = theme.getScrollbarWidth(),
                count = $el.find('> *').length,
                w_xs = 481 - scrollWidth,
                w_md = 768 - scrollWidth,
                w_lg = 992 - scrollWidth;

            if ($el.find('.product-col').get(0)) {
                portoCalcSliderMargin($slider_wrapper, $el.find('.product-col').css('padding-left'));
                portoCalcSliderButtonsPosition($slider_wrapper, $el.find('.product-col').css('padding-left'));
            }
            portoCalcSliderTitleLine($slider_wrapper);

            if (single) {
                items = 1;
            } else {
                items = lg ? lg : 1;
                if (lg) responsive[w_lg] = { items: lg, loop: (this.options.loop && count > lg) ? true : false };
                if (md) responsive[w_md] = { items: md, loop: (this.options.loop && count > md) ? true : false };
                if (xs) responsive[w_xs] = { items: xs, loop: (this.options.loop && count > xs) ? true : false };
                if (ls) responsive[0] = { items: ls, loop: (this.options.loop && count > ls) ? true : false };
            }

            this.options = $.extend(true, {}, this.options, {
                loop: (this.options.loop && count > items) ? true : false,
                items : items,
                responsive : responsive,
                onRefresh: function() {
                    if ($el.find('.product-col').get(0)) {
                        portoCalcSliderMargin($slider_wrapper, $el.find('.product-col').css('padding-left'));
                        portoCalcSliderButtonsPosition($slider_wrapper, $el.find('.product-col').css('padding-left'));
                    }
                    portoCalcSliderTitleLine($slider_wrapper);
                },
                onInitialized: function() {
                    if ($el.find('.product-col').get(0)) {
                        portoCalcSliderButtonsPosition($slider_wrapper, $el.find('.product-col').css('padding-left'));
                    }
                },
                touchDrag: (count == 1) ? false : true,
                mouseDrag: (count == 1) ? false : true
            });

            // Auto Height Fixes
            if (this.options.autoHeight) {
                function calcOwlHeight() {
                    var h = 0;
                    $el.find('.owl-item.active').each(function() {
                        if (h < $(this).height())
                            h = $(this).height();
                    });
                    $el.find('.owl-stage-outer').height( h );
                }
                $(window).on('resize', function() {
                    calcOwlHeight();
                });

                $(window).on('load', function() {
                    calcOwlHeight();
                });
            }

            $el.owlCarousel(this.options);

            return this;
        }
    };

    // expose to scope
    $.extend(theme, {
        WooProductsSlider: WooProductsSlider
    });

    // jquery plugin
    $.fn.themeWooProductsSlider = function(opts) {
        return this.map(function() {
            var $this = $(this);

            if ($this.data(instanceName)) {
                return $this.data(instanceName);
            } else {
                return new theme.WooProductsSlider($this, opts);
            }

        });
    }

}).apply(this, [window.theme, jQuery]);

// Products Infinite
(function(theme, $) {

    theme = theme || {};

    $.extend(theme, {

        ProductsInfinite: {

            defaults: {
                elements: '.products-infinite',
                itemSelector: '.products-infinite .product'
            },

            initialize: function($elements, itemSelector) {
                this.$elements = ($elements || $(this.defaults.elements));
                this.itemSelector = (itemSelector || this.defaults.itemSelector);

                this.build();
            },

            build: function() {
                var self = this;

                self.$elements.each(function() {
                    var $this = $(this),
                        curr_page = $this.attr('data-pagenum'),
                        max_page = $this.attr('data-pagemaxnum'),
                        page_path = $this.attr('data-path');
                    $this.infinitescroll($.extend(theme.infiniteConfig, {
                        navSelector  : '.woocommerce-pagination',
                        nextSelector : '.woocommerce-pagination .page-numbers a.next',
                        itemSelector : self.itemSelector,
                        state : {
                            currPage: curr_page
                        },
                        maxPage: max_page,
                        pathParse : function(a, b) {
                            return [page_path, '/'];
                        }
                    }), function(posts) {
                        var $posts = $(posts);
                        theme.refreshVCContent($posts);
                        porto_init();
                        porto_woocommerce_init();
                    });
                });

                return self;
            }
        }

    });

}).apply(this, [window.theme, jQuery]);

// Woocommerce Shop ToolBar Events
(function(theme, $) {

    $(function() {

        $(document).on('click', '#grid', function(e) {
            e.preventDefault();
            $(this).addClass('active');
            $('#list').removeClass('active');
            if (($.cookie && $.cookie('gridcookie') == 'list') || !$.cookie) {
                var $toggle = $('.gridlist-toggle');
                if ($toggle.length) {
                    var $parent = $toggle.parent().parent();
                    var $products = $parent.find('ul.products');
                    $products.fadeOut(300, function() {
                        $products.addClass('grid').removeClass('list').fadeIn(300);
                        theme.refreshVCContent();
                        $('ul.products.grid > li.show-outimage-q-onimage .product-inner, ul.products.grid > li.show-links-outimage .product-inner, ul.products.grid > li.show-outimage-q-onimage-alt .product-inner').each(function(){
                            $(this).children('.product-loop-title').before($(this).children('.rating-wrap'));
                        });
                    });
                }
            }
            if ($.cookie)
                $.cookie('gridcookie', 'grid', { path: '/' });
            return false;
        });

        $(document).on('click', '#list', function(e) {
            e.preventDefault();
            $(this).addClass('active');
            $('#grid').removeClass('active');
            if (($.cookie && $.cookie('gridcookie') == 'grid') || !$.cookie) {
                var $toggle = $('.gridlist-toggle');
                if ($toggle.length) {
                    var $parent = $toggle.parent().parent();
                    var $products = $parent.find('ul.products');
                    $products.fadeOut(300, function() {
                        $products.addClass('list').removeClass('grid').fadeIn(300);
                        theme.refreshVCContent();
                        $('ul.products.list > li.show-outimage-q-onimage .product-inner, ul.products.list > li.show-links-outimage .product-inner, ul.products.list > li.show-outimage-q-onimage-alt .product-inner').each(function(){
                            $(this).children('.product-loop-title').after($(this).children('.rating-wrap'));
                        });
                    });
                }
            }
            if ($.cookie)
                $.cookie('gridcookie', 'list', { path: '/' });
            return false;
        });
    });

}).apply(this, [window.theme, jQuery]);


// Woocommerce Add to Cart, View Cart Events
(function(theme, $) {

    var $supports_html5_storage;
    try {
        $supports_html5_storage = ( 'sessionStorage' in window && window.sessionStorage !== null );

        window.sessionStorage.setItem( 'wc', 'test' );
        window.sessionStorage.removeItem( 'wc' );
    } catch( err ) {
        $supports_html5_storage = false;
    }

    var setCartCreationTimestamp = function() {
        if ( $supports_html5_storage ) {
            sessionStorage.setItem( 'wc_cart_created', ( new Date() ).getTime() );
        }
    };

    var setCartHash = function(cart_hash) {
        if ( $supports_html5_storage ) {
            localStorage.setItem( 'wc_cart_hash', cart_hash );
            sessionStorage.setItem( 'wc_cart_hash', cart_hash );
        }
    };

    var initAjaxRemoveCartItem = function() {
        $('#mini-cart .cart_list').scrollbar();
        $(document).off('click', '.widget_shopping_cart .remove-product, .shop_table.cart .remove-product').on('click', '.widget_shopping_cart .remove-product, .shop_table.cart .remove-product', function(e){
            e.preventDefault();
            var $this = $(this);
            var cart_id = $this.data("cart_id");
            var product_id = $this.data("product_id");
            $this.closest('li').find('.ajax-loading').show();

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: theme.ajax_url,
                data: {
                    action: "porto_cart_item_remove",
                    cart_id: cart_id
                },
                success: function( response ) {
                    var this_page = window.location.toString(),
                        item_count = $(response.fragments['div.widget_shopping_cart_content']).find('.mini_cart_item').length;

                    this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );
                    updateCartFragment(response);
                    $( document.body ).trigger( 'wc_fragments_refreshed' );
                    $('.viewcart-' + product_id).removeClass('added');
                    $('.porto_cart_item_' + cart_id).remove();

                    // Block widgets and fragments
                    if ( item_count == 0 && ($('body').hasClass('woocommerce-cart') || $('body').hasClass('woocommerce-checkout')) ) {
                        $( '.page-content' ).fadeTo( '400', '0.8' ).block({
                            message: null,
                            overlayCSS: {
                                opacity: 0.2
                            }
                        });
                    } else {
                        $( '.shop_table.cart, .shop_table.review-order, .updating, .cart_totals' ).fadeTo( '400', '0.8' ).block({
                            message: null,
                            overlayCSS: {
                                opacity: 0.2
                            }
                        });
                    }

                    // Unblock
                    $( '.widget_shopping_cart, .updating' ).stop( true ).css( 'opacity', '1' ).unblock();

                    // Cart page elements
                    if ( item_count == 0 && ($('body').hasClass('woocommerce-cart') || $('body').hasClass('woocommerce-checkout')) ) {
                        $( '.page-content' ).load( this_page + ' .page-content:eq(0) > *', function() {
                            $( '.page-content' ).stop( true ).css( 'opacity', '1' ).unblock();
                        });
                    } else {
                        $( '.shop_table.cart' ).load( this_page + ' .shop_table.cart:eq(0) > *', function() {
                            $( '.shop_table.cart' ).stop( true ).css( 'opacity', '1' ).unblock();
                        });

                        $( '.cart_totals' ).load( this_page + ' .cart_totals:eq(0) > *', function() {
                            $( '.cart_totals' ).stop( true ).css( 'opacity', '1' ).unblock();
                        });

                        // Checkout page elements
                        $( '.shop_table.review-order' ).load( this_page + ' .shop_table.review-order:eq(0) > *', function() {
                            $( '.shop_table.review-order' ).stop( true ).css( 'opacity', '1' ).unblock();
                        });
                    }
                }
            });

            return false;
        });
    };

    var refreshCartFragment = function() {
        initAjaxRemoveCartItem();
        if ( $.cookie( 'woocommerce_items_in_cart' ) > 0 ) {
            $( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).show();
        } else {
            $( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).hide();
        }
    };

    var updateCartFragment = function(data) {
        if (data && data.fragments) {
            var fragments = data.fragments,
                cart_hash = data.cart_hash;

            $.each(fragments, function(key, value) {
                $(key).replaceWith(value);
            });
            if ( typeof wc_cart_fragments_params === 'undefined' ) {
                return;
            }
            /* Storage Handling */
            if ( $supports_html5_storage ) {
                var prev_cart_hash = sessionStorage.getItem( 'wc_cart_hash' );

                if ( prev_cart_hash === null || prev_cart_hash === undefined || prev_cart_hash === '' ) {
                    setCartCreationTimestamp();
                }
                sessionStorage.setItem( wc_cart_fragments_params.fragment_name, JSON.stringify( fragments ) );
                setCartHash( cart_hash );
            }
        }
    };

    $(function() {

        refreshCartFragment();

        // add ajax cart loading
        $(document).on('click', '.add_to_cart_button', function(e) {
            var $this = $(this);
            if ( $this.is('.product_type_simple') ) {
                if ( $this.attr('data-product_id') ) {
                    $this.addClass('product-adding');
                }
                //add to cart notifaction style 2
                if( $this.hasClass('viewcart-style-2') ){
                    $('body').append('<div id="loading-mask"><div class="background-overlay"></div></div>');
                    $(this).closest('.product').find('.loader-container').show();
                }
            }
        });

        // add to cart action
        $(document).on('click', 'span.add_to_cart_button', function(e) {
            var $this = $(this);
            if ( $this.is('.product_type_simple') ) {
                if ( !$this.attr('data-product_id') ) {
                    window.location.href = $this.attr('href');
                }
            } else {
                window.location.href = $this.attr('href');
            }
        });

        $('body').bind('added_to_cart', function() {
            $('ul.products li.product .added_to_cart').remove();
            initAjaxRemoveCartItem();
        });

        $(document.body).bind('wc_fragments_refreshed wc_fragments_loaded', function() {
            refreshCartFragment();
        });

        $(document).on( 'click', '.product-image .viewcart, .after-loading-success-message .viewcart', function( e ){
            var link = $(this).attr('data-link');
            window.location.href = link;
            e.preventDefault();
        });
        var $msg = '';
        $( document ).on( 'added_to_cart', 'body', function(event) {
            $('.add_to_cart_button.product-adding').each(function() {
                var $link = $(this);
                if ($link.hasClass('viewcart-style-1')) {
                    $link.removeClass('product-adding');
                    $link.closest('.product').find('.viewcart').addClass('added');
                } else {
                    //add to cart notifaction style 2
                    $link.removeClass('product-adding');
                    $('body #loading-mask').remove();
                    $link.closest('.product').find('.loader-container').hide();
                    $msg = $link.closest('.product').find('.after-loading-success-message');
                    $('body').append($msg.clone().show());
                    setTimeout(function() { $('body > .after-loading-success-message').remove(); }, 4000);
                    $('.continue_shopping').click(function(){ $('body > .after-loading-success-message').fadeOut(200, function() { $(this).remove(); }); });
                }
            });
        });

        $(document).on("click", ".variations_form .variations .filter-item-list .filter-color, .variations_form .variations .filter-item-list .filter-item", function(e) {
            e.preventDefault();
            if ($(this).closest("ul").next("select").length < 1) {
                return;
            }
            var value = unescape($(this).data("value")),
                selector = $(this).closest("ul").next("select");
            if ($(this).closest("li").hasClass("active")) {
                $(this).closest("li").removeClass("active");
                selector.children("option:selected").removeAttr("selected");
                selector.val('');
            } else {
                $(this).closest("ul").children("li").removeClass("active");
                $(this).closest("li").addClass("active");
                selector.children("option:selected").removeAttr("selected");
                selector.children("option[value='" + value + "']").attr("selected", "selected");
                selector.val(selector.children("option[value='" + value + "']").val());
            }
            selector.change();
        });
        $(document).on('wc_variation_form', '.variations_form', function() {
            if ($(this).find(".filter-item-list").length < 1) {
                return;
            }
            $(this).find(".variations select").trigger("focusin");
        });
        $(document).on('updated_wc_div', function() {
            $('.woocommerce-cart-form .porto-lazyload').themePluginLazyLoad();
        });
        $(document).on('found_variation reset_data', '.variations_form', function(e) {
            if ($(this).find(".filter-item-list").length < 1) {
                return;
            }
            $(this).find(".filter-item-list").each(function() {
                if ($(this).next("select").length < 1) {
                    return;
                }
                var selector = $(this).next("select"),
                    html = '';
                selector.children("option").each(function() {
                    var isColor = typeof $(this).data('color') != 'undefined' ? true : false,
                    spanClass = isColor ? "filter-color" : "filter-item";
                    if (!$(this).val()) {
                        return;
                    }
                    html += '<li';
                    if ($(this).val() == selector.val()) {
                        html += ' class="active"';
                    }
                    html += '><a href="#" data-value="'+ escape( $(this).val() ) +'" class="' + spanClass + '"';
                    if (isColor) {
                        html += ' style="background-color: #' + escape( $(this).data('color').replace('#','') ) + '"';
                    }
                    html += '>';
                    if (!isColor) {
                        html += $(this).text();
                    }
                    html += '</a></li>';
                });
                $(this).html(html);
            });
        });
        /*$(document).on('woocommerce_variation_select_change', '.variations_form', function(e) {
            if ($(this).closest('.product-summary-wrap').find('p.price.d-none').length) {
                $(this).closest('.product-summary-wrap').find('p.price:visible').html($(this).closest('.product-summary-wrap').find('p.price.d-none').html());
            }
            $(this).children('.single-variation-msg').hide();
            if ($(this).closest('.product-summary-wrap').find('.single_add_to_cart_button').length) {
                $(this).closest('.product-summary-wrap').find('.single_add_to_cart_button').removeAttr('disabled');
            }
        });
        $(document).on('show_variation', '.variations_form .single_variation', function(e, variation, purchasable) {
            if (!$(this).closest('.product-summary-wrap').find('p.price.d-none').length) {
                $(this).closest('.product-summary-wrap').find('p.price').clone().addClass('d-none').insertAfter($(this).closest('.product-summary-wrap').find('p.price'));
            }
            $(this).hide();
            if (purchasable && $(this).closest('.product-summary-wrap').find('.single_add_to_cart_button').length) {
                $(this).closest('.product-summary-wrap').find('.single_add_to_cart_button').removeAttr('disabled');
            } else {
                $(this).closest('.product-summary-wrap').find('.single_add_to_cart_button').attr('disabled', 'disabled');
            }
            if (variation.variation_is_visible) {
                if (variation.price_html) {
                    $(this).closest('.product-summary-wrap').find('p.price:visible').html($(this).html());
                } else if ($(this).closest('.product-summary-wrap').find('p.price.d-none').length) {
                    $(this).closest('.product-summary-wrap').find('p.price:visible').html($(this).closest('.product-summary-wrap').find('p.price.d-none').html());
                }
            } else {
                if (!$(this).closest('form').children('.single-variation-msg').length) {
                    $('<div class="single-variation-msg" style="display: none;"></div>').prependTo($(this).closest('form'));
                }
                $(this).closest('form').children('.single-variation-msg').show().html($(this).html()).show();
            }
            if ( !purchasable ) {
                $(this).closest('.product-summary-wrap').find('p.price:visible').find('.price').css('text-decoration', 'line-through');
            }
        });*/
        $('.product-images').magnificPopup(
            $.extend(true, {}, theme.mfpConfig, {
                delegate: '.img-thumbnail a.zoom',
                type: 'image',
                gallery: { enabled:true }
            })
        );
    });

}).apply(this, [window.theme, jQuery]);


// Woocommerce Category Filter
(function(theme, $) {

    /**
     Copyright (c) 2010, All Right Reserved, Wong Shek Hei @ shekhei@gmail.com
     License: GNU Lesser General Public License (http://www.gnu.org/licenses/lgpl.html)
     **/
    var expr = /[.#\w].([\S]*)/g, classexpr = /(?!(\[))(\.)[^.#[]*/g, idexpr = /(#)[^.#[]*/, tagexpr = /^[\w]+/, varexpr = /(\w+?)=(['"])([^\2$]*?)\2/, simpleselector = /^[\w]+$/, parseSelector = function (d) {
        for (var c = {sel: [], val: []}, a = [], j = !1, h = "", e = [], f = 0, m = d.length; f < m; f++) {
            var g = d.charAt(f);
            if (j)if ("\\" === g && f + 1 < d.length)e.push(d.charAt(++f)); else if (h === g)h = "", e.push(g); else if (("'" === g || '"' === g) && "" === h)h = g, e.push(g); else if ("]" === g && "" === h)c.val.push(e.join("")), e = [], j = !1; else {
                if ("]" !== g || "" !== h)"" === h && "," === g ? (c.val.push(e.join("")),
                    e = []) : e.push(g)
            } else"\\" === g && f + 1 < d.length ? j && e.push(d.charAt(++f)) : "[" === g && "" === h ? j = !0 : " " === g || "+" === g ? (c.sel = c.sel.join(""), a.push(c), "+" === g && a.push({sel: "+", val: ""}), c = {sel: [], val: []}) : " " !== g && "]" !== g && c.sel.push(g)
        }
        if (0 != c.sel.length || 0 != c.val.length)c.sel = c.sel.join(""), a.push(c);
        for (f = 0; f < a.length; f++) {
            c = a[f].sel;
            if ("+" === c)b.tag = c; else {
                var b = [];
                b.tag = tagexpr.exec(c);
                b.id = idexpr.exec(c);
                b.id && $.isArray(b.id) && (b.id = b.id[0].substr(1));
                b.tag || (b.tag = "div");
                b.vars = [];
                for (d = 0; d < a[f].val.length; d++)h =
                    a[f].val[d].indexOf("="), j = a[f].val[d].substr(0, h), h = a[f].val[d].substr(h + 1), h = h.replace(/^[\s]*[\"\']*|[\"\']*[\s]*$/g, ""), "text" === j ? b.text = h : b.vars.push([j, h]);
                c = c.match(classexpr);
                j = [];
                if (c) {
                    for (d = 0; d < c.length; d++)j.push(c[d].substr(1));
                    b.className = j.join(" ")
                }
            }
            a[f] = b
        }
        return a
    }, rmFromParent = function (d) {
        var c = d.parentNode, a = d.nextSibling;
        c.removeChild(d);
        return a ? function () {
            c.insertBefore(d, a)
        } : function () {
            c.appendChild(d)
        }
    }, nonArrVer = function (d, c) {
        var a = [], a = simpleselector.test(d) ? [
                {tag: d}
            ] : parseSelector(d),
            j = [];
        "undefined" === typeof c && (c = 1);
        for (var h = [], e = [], f = [], m = document.createElement("div"), g = 0, b = 0; b < a.length; b++) {
            if ("+" == a[b].tag)e = f.slice(), --g; else {
                for (var l = 0; l < c; l++)if ("input" == a[b].tag) {
                    var k = [];
                    k.push("<" + a[b].tag);
                    a[b].id && k.push("id='" + a[b].id + "'");
                    a[b].className && (k.push("class='" + a[b].className), b + 1 === a.length && k.push(lastClass), k.push("'"));
                    if (a[b].vars)for (var n = 0; n < a[b].vars.length; n++)k.push(a[b].vars[n][0] + "='" + a[b].vars[n][1] + "'");
                    a[b].text && k.push("value='" + a[b].text + "'");
                    k.push("/>");
                    f[l] = e[l];
                    e[l] ? (e[l].innerHTML += k.join(" "), e[l] = e[l].lastChild) : (m.innerHTML = k.join(" "), e[l] = m.removeChild(m.firstChild))
                } else {
                    k = document.createElement(a[b].tag);
                    if (a[b].vars)for (n = 0; n < a[b].vars.length; n++)k.setAttribute(a[b].vars[n][0], a[b].vars[n][1]);
                    a[b].id && (k.id = a[b].id);
                    a[b].className && (k.className = a[b].className);
                    a[b].text && k.appendChild(document.createTextNode(a[b].text));
                    f[l] = e[l];
                    e[l] = e[l] ? e[l].appendChild(k) : k
                }
                g++ || Array.prototype.push.apply(h, e)
            }
            j =
                $.merge(j, e)
        }
        return $(h)
    }, arrVer = function (d, c, a) {
        for (var j = d.match(/%[^%]*%/g) || [], h = [], e = 0; e < c.length; e++) {
            for (var f = d, m = 0; m < j.length; m++)var g = j[m].substr(1, j[m].length - 2), f = f.replace(j[m], c[e][g]);
            h = $.merge(h, nonArrVer(f, a))
        }
        return $(h)
    };

    $.porto_jseldom = function (d) {
        if (2 == arguments.length && $.isPlainObject(arguments[1]))return arrVer.apply(this, [arguments[0], [arguments[1]]]);
        if (1 == arguments.length || 2 == arguments.length && !$.isArray(arguments[1]))return nonArrVer.apply(this, arguments);
        if (2 == arguments.length)return arrVer.apply(this, arguments)
    };

    var refreshPriceSlider = function() {

        var $price_slider = $('.price_slider');

        if ($price_slider.length) {
            // woocommerce_price_slider_params is required to continue, ensure the object exists
            if ( typeof woocommerce_price_slider_params === 'undefined' ) {
                return false;
            }

            // Get markup ready for slider
            $( 'input#min_price, input#max_price' ).hide();
            $( '.price_slider, .price_label' ).show();

            // Price slider uses jquery ui
            var min_price = $( '.price_slider_amount #min_price' ).data( 'min' ),
                max_price = $( '.price_slider_amount #max_price' ).data( 'max' ),
                current_min_price = parseInt( $( '.price_slider_amount #min_price').val() ? $( '.price_slider_amount #min_price').val() : min_price, 10 ),
                current_max_price = parseInt( $( '.price_slider_amount #max_price').val() ? $( '.price_slider_amount #max_price').val() : max_price, 10 );

            $( '.price_slider' ).slider({
                range: true,
                animate: true,
                min: min_price,
                max: max_price,
                values: [ current_min_price, current_max_price ],
                create: function() {

                    $( '.price_slider_amount #min_price' ).val( current_min_price );
                    $( '.price_slider_amount #max_price' ).val( current_max_price );

                    $( document.body ).trigger( 'price_slider_create', [ current_min_price, current_max_price ] );
                },
                slide: function( event, ui ) {

                    $( 'input#min_price' ).val( ui.values[0] );
                    $( 'input#max_price' ).val( ui.values[1] );

                    $( document.body ).trigger( 'price_slider_slide', [ ui.values[0], ui.values[1] ] );
                },
                change: function( event, ui ) {

                    $( document.body ).trigger( 'price_slider_change', [ ui.values[0], ui.values[1] ] );
                }
            });
        }

        // remove filter loading
        $('.yith-woo-ajax-navigation, .yith-wcan-list-price-filter').removeClass('loading');
    };

    var categoryAjaxProcess = function(href, updateSelect2) {
        var shop_before = '.shop-loop-before',
            shop_after = '.shop-loop-after',
            shop_container = '.archive-products .products',
            shop_info = '.archive-products .woocommerce-info',
            $shop_parent = $(shop_before).parent(),
            $sticky_sidebar = $('.sidebar [data-plugin-sticky]'),
            show_toolbar = $(shop_before).data('show');

        if (show_toolbar)
            $(shop_before + ',' + shop_after).stop(true).fadeTo('400','1').block({message: null, overlayCSS: {opacity: 0.2}});
        if ($(shop_container).length)
            $(shop_container).addClass('yith-wcan-loading');
        else
            $(shop_info).html('').addClass('yith-wcan-loading products');

        if ($sticky_sidebar.get(0)) {
            //$shop_parent.css('min-height', $sticky_sidebar.height());
            theme.refreshStickySidebar(false);
        }

        theme.scrolltoContainer(show_toolbar ? $(shop_before) : $(shop_container));

        $('.yith-woo-ajax-navigation, .yith-wcan-list-price-filter').addClass('loading');

        var cart_content, widget_cart;

        if (widget_cart = $('.sidebar-content .widget_shopping_cart').get(0)) {
            cart_content = $(widget_cart).html();
        }

        $.ajax({
            url    : href,
            success: function (response) {
                var $parent = $(shop_container).parent(),
                    $response = $(response);

                if ($(shop_container).length) {
                    $(shop_container).html('');
                }
                if ($sticky_sidebar.get(0))
                    $shop_parent.css('min-height', 0);

                // products container
                if ($response.find(shop_container).length) {
                    $parent.html($response.find(shop_container));
                } else {
                    $parent.html($response.find('.woocommerce-info'));
                    $parent.find('.woocommerce-info').addClass('products');
                }

                if ($(shop_before + ',' + shop_after).get(0))
                    $(shop_before + ',' + shop_after).stop(true).css('opacity', '1').unblock();

                // top toolbar
                if ($response.find(shop_before).length) {
                    if ($(shop_before).length == 0) {
                        $.porto_jseldom(shop_before).insertAfter($(shop_container));
                    }

                    $(shop_before)
                        .html($response.find(shop_before).html())
                        .show();
                } else {
                    $(shop_before).empty();
                }

                // bottom toolbar
                if ($response.find(shop_after).length) {
                    $(shop_after).html($response.find(shop_after).html()).show();
                } else {
                    $(shop_after).empty();
                }

                $('.sidebar-content').each(function(index) {
                    var $this = $(this),
                        $that = $($response.find('.sidebar-content').get(index));

                    $this.html($that.html());

                    if (typeof updateSelect2 != 'undefined' && updateSelect2) {
                        // Use Select2 enhancement if possible
                        if ( jQuery().selectWoo ) {
                            var porto_wc_layered_nav_select = function() {
                                $this.find( 'select.woocommerce-widget-layered-nav-dropdown' ).each(function() {
                                    $(this).selectWoo( {
                                        placeholder: $(this).find('option').eq(0).text(),
                                        minimumResultsForSearch: 5,
                                        width: '100%',
                                        allowClear: typeof $(this).attr('multiple') != 'undefined' && $(this).attr('multiple') == 'multiple' ? 'false' : 'true'
                                    } );
                                });
                            };
                            porto_wc_layered_nav_select();
                        }
                        $('body').children('span.select2-container').remove();
                    }
                });

                var $script = $response.filter('script:contains("var woocommerce_price_slider_params")').first();
                if ($script && $script.length) {
                    eval($script.text());
                    window.woocommerce_price_slider_params = woocommerce_price_slider_params;
                } else {
                    window.woocommerce_price_slider_params = undefined;
                }

                //update browser history (IE doesn't support it)
                if (!navigator.userAgent.match(/msie/i)) {
                    window.history.pushState({"pageTitle": response.pageTitle}, "", href);
                }

                //trigger ready event
                $(document).trigger("yith-wcan-ajax-filtered");

                if (widget_cart = $('.sidebar-content .widget_shopping_cart').get(0)) {
                    $('.sidebar-content .widget_shopping_cart').html(cart_content);
                    if ( $.cookie( 'woocommerce_items_in_cart' ) > 0 ) {
                        $( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).show();
                    } else {
                        $( '.hide_cart_widget_if_empty' ).closest( '.widget_shopping_cart' ).hide();
                    }
                }
            }
        });
    };

    var categoryAjax = function () {
        // add class in price filter widget
        $('.widget_price_filter').addClass('yith-wcan-list-price-filter');

        if (theme.category_ajax) {

            // order by ajax
            $( '.woocommerce-ordering' ).off( 'change', 'select.orderby' ).on( 'change', 'select.orderby', function(e) {
                e.preventDefault();

                var $this = $(this),
                    $form = $this.closest('form'),
                    href = '?' + $form.serialize();

                categoryAjaxProcess(href);
            });

            // view ajax
            $( '.woocommerce-viewing' ).off( 'change', 'select.count' ).on( 'change', 'select.count', function(e) {
                e.preventDefault();

                var $this = $(this),
                    $form = $this.closest('form'),
                    href = '?' + $form.serialize();

                categoryAjaxProcess(href);
            });

            // pagination ajax
            $( '.woocommerce-pagination' ).off( 'click', 'a.page-numbers' ).on( 'click', 'a.page-numbers', function(e) {
                e.preventDefault();

                var href = this.href;

                categoryAjaxProcess(href);
            });

            // yith filter
            $(document).off('click', '.yith-wcan a').on('click', '.yith-wcan a', function (e) {
                $(this).yith_wcan_ajax_filters(e, this);
            });

            // price filter ajax
            $( '.widget_price_filter .price_slider_wrapper').off( 'click', '.button').on( 'click', '.button', function(e) {
                e.preventDefault();

                var $this = $(this),
                    $form = $this.closest('form'),
                    action = $form.attr('action'),
                    href = action + '?' + $form.serialize(),
                    $count = $('.woocommerce-viewing select.count');

                if ($count.length) {
                    var count = $('.woocommerce-viewing select.count').val();
                    if (count != $count.find('option:not([disabled]):first').val()) {
                        href += '&count=' + count;
                    }
                }

                $('.widget_price_filter').removeClass('yith-wcan-list-price-filter');

                categoryAjaxProcess(href);
            });
            $( '.porto_widget_price_filter').off( 'click', '.button').on( 'click', '.button', function(e) {
                e.preventDefault();

                var $this = $(this),
                    $form = $this.closest('form'),
                    action = $form.attr('action'),
                    href = action + '?' + $form.serialize(),
                    $count = $('.woocommerce-viewing select.count');

                if ($count.length) {
                    var count = $('.woocommerce-viewing select.count').val();
                    if (count != $count.find('option:not([disabled]):first').val()) {
                        href += '&count=' + count;
                    }
                }

                categoryAjaxProcess(href);
            });

            // layerd nav filter
            $('.widget_layered_nav, .widget_rating_filter, .widget_layered_nav_filters').off('click', 'a').on('click', 'a', function(e) {
                if ($(this).hasClass('yit-wcan-select-open'))
                    return;

                e.preventDefault();

                var $this = $(this),
                    href = $this.attr('href'),
                    $count = $('.woocommerce-viewing select.count');

                if ($count.length) {
                    var count = $('.woocommerce-viewing select.count').val();
                    if (count != $count.find('option:not([disabled]):first').val()) {
                        href += '&count=' + count;
                    }
                }

                var yith_select = $this.closest('.yith-wcan-select');
                if (yith_select.get(0)) {
                    yith_select.parent().css({"opacity":0, "z-index":-1});
                }

                categoryAjaxProcess(href);

                return false;
            });
            $('.widget_layered_nav select').off('change').on('change', function(e) {
                e.preventDefault();

                var $this = $(this),
                    name = $this.closest('form').find('input[type=hidden]').length ? $this.closest('form').find('input[type=hidden]').attr('name').replace('filter_', '') : $this.attr('class').replace('dropdown_layered_nav_', ''),
                    slug = $this.val(),
                    href,
                    $count = $('.woocommerce-viewing select.count');

                href = window.location.href;
                href = href.replace(/\/page\/\d+/, "").replace("&amp;", '&').replace("%2C", ',');
                var u = new Url(href);

                u.query['filtering'] = 1;
                u.query['filter_' + name] = slug;
                if ($count.length) {
                    var count = $('.woocommerce-viewing select.count').val();
                    if (count != $count.find('option:not([disabled]):first').val()) {
                        u.query['count'] = count;
                    }
                }

                href = u.toString();
                categoryAjaxProcess(href, name);
                return false;
            });
        } else {
            $(document).on('change', '.woocommerce-viewing select.count', function() {
                $(this).closest('form').submit();
            });
        }
    };

    var ajaxFiltered = function() {
        var shop_before = '.shop-loop-before',
            shop_after = '.shop-loop-after',
            shop_container = '.archive-products .products',
            $shop_parent = $(shop_before).parent(),
            $sticky_sidebar = $('.sidebar [data-plugin-sticky]');

        if ($sticky_sidebar.get(0)) {
            $shop_parent.css('min-height', 0);
        }

        if ($(shop_before + ',' + shop_after).get(0))
            $(shop_before + ',' + shop_after).stop(true).fadeTo('400','1').unblock();
        if ($(shop_container).find('.product').get(0)) {
            $(shop_before + ',' + shop_after).show().data('show', true);
        } else {
            $(shop_before + ',' + shop_after).hide().data('show', false);
        }

        porto_init();
        porto_woocommerce_init();

        $( '.woocommerce-ordering' ).off( 'change', 'select.orderby' ).on( 'change', 'select.orderby', function() {
            $( this ).closest( 'form' ).submit();
        });

        // category ajax
        refreshPriceSlider();
        categoryAjax();
    };

    $(function() {
        // yith woo ajax filter events
        if (typeof yith_wcan != 'undefined') {
            yith_wcan.container = '.archive-products .products';
            yith_wcan.pagination = '.shop-loop-before';
            yith_wcan.result_count = '.shop-loop-after';
        }

        $(document).on('click', '.yith-wcan a', function(e){
            // add price filter loading
            var shop_before = '.shop-loop-before',
                shop_after = '.shop-loop-after',
                shop_container = '.archive-products .products',
                shop_info = '.archive-products .woocommerce-info',
                $shop_parent = $(shop_before).parent(),
                $sticky_sidebar = $('.sidebar [data-plugin-sticky]'),
                show_toolbar = $(shop_before).data('show');

            if (show_toolbar)
                $(shop_before + ',' + shop_after).stop(true).show().fadeTo('400','0.8').block({message: null, overlayCSS: {opacity: 0.2}});
            if ($(shop_container).length)
                $(shop_container).html('').addClass('yith-wcan-loading');
            else
                $(shop_info).html('').addClass('yith-wcan-loading products');

            if ($sticky_sidebar.get(0)) {
                //$shop_parent.css('min-height', $sticky_sidebar.height());
                theme.refreshStickySidebar(false);
            }
            $('.yith-woo-ajax-navigation, .yith-wcan-list-price-filter').addClass('loading');
            theme.scrolltoContainer(show_toolbar ? $(shop_before) : $(shop_container));
        });

        $(document).ready(function() {
            ajaxFiltered();
        });

        $(document).on('yith-wcan-ajax-filtered', function() {
            ajaxFiltered();
        });

        categoryAjax();

        // product filter ajax
        if (theme.prdctfltr_ajax) {
            // select count
            $(document).on( 'change', '.woocommerce-viewing select.count', function() {
                $( this ).closest( 'form' ).submit();
            });
            // page number
            $(document).on( 'click', '.woocommerce-pagination a.page-numbers', function(e) {
                theme.scrolltoContainer($('.shop-loop-before'));
            });
        }
    });

}).apply(this, [window.theme, jQuery]);


// Woocommerce Product Image Slider
(function(theme, $) {

    theme = theme || {};

    var duration = 300,
        flag = false,
        thumbs_count = theme.product_thumbs_count;

    if (theme.product_zoom && (!('ontouchstart' in document) || (('ontouchstart' in document) && theme.product_zoom_mobile))) {
        var zoomConfig = {
            responsive: true,
            zoomWindowFadeIn: 200,
            zoomWindowFadeOut: 100,
            zoomType: js_porto_vars.zoom_type,
            cursor: 'grab'
        };

        if (js_porto_vars.zoom_type == 'lens') {
            zoomConfig.scrollZoom = js_porto_vars.zoom_scroll;
            zoomConfig.lensSize = js_porto_vars.zoom_lens_size;
            zoomConfig.lensShape = js_porto_vars.zoom_lens_shape;
            zoomConfig.containLensZoom = js_porto_vars.zoom_contain_lens;
            zoomConfig.lensBorder = js_porto_vars.zoom_lens_border;
            zoomConfig.borderColour = js_porto_vars.zoom_border_color;
        }

        if (js_porto_vars.zoom_type == 'inner') {
            zoomConfig.borderSize = 0;
        } else {
            zoomConfig.borderSize = js_porto_vars.zoom_border;
        }
    }

    $.extend(theme, {

        WooProductImageSlider: {

            defaults: {
                elements: '.product-image-slider'
            },

            initialize: function($elements) {
                this.$elements = ($elements || $(this.defaults.elements));

                this.build();

                return this;
            },

            build: function() {
                var self = this;

                self.$elements.each(function() {
                    var $this = $(this),
                        $product = $this.closest('.product'),
                        $thumbs_slider = $product.find('.product-thumbs-slider'),
                        $thumbs = $product.find('.product-thumbnails-inner'),
                        $thumbs_vertical_slider = $product.find('.product-thumbs-vertical-slider'),
                        currentSlide = 0,
                        count = $this.find('> *').length;

                    $this.find('> *:first-child').waitForImages(true).done(function() {

                        $thumbs_slider.owlCarousel({
                            rtl: theme.rtl,
                            loop : false,
                            autoplay : false,
                            items : thumbs_count,
                            nav: false,
                            navText: ["", ""],
                            dots: false,
                            rewind: true,
                            margin: 6,
                            stagePadding: 1,
                            lazyLoad: true,
                            onInitialized: function() {
                                self.selectThumb(null, $thumbs_slider, 0);
                                if ($thumbs_slider.find('.owl-item').length >= thumbs_count)
                                    $thumbs_slider.append('<div class="thumb-nav"><div class="thumb-prev"></div><div class="thumb-next"></div></div>');
                            }
                        }).on('click', '.owl-item', function() {
                            self.selectThumb($this, $thumbs_slider, $(this).index());
                        });
                        if ($thumbs_vertical_slider.length > 0) {
                            $thumbs_vertical_slider.slick({
                                dots: false,
                                vertical: true,
                                slidesToShow: thumbs_count > 2 ? thumbs_count - 1 : thumbs_count,
                                slidesToScroll: 1
                            }).on('click', '.img-thumbnail', function() {
                                self.selectVerticalSliderThumb($this, $thumbs_vertical_slider, $(this).data('slick-index'));
                            });
                            self.selectVerticalSliderThumb(null, $thumbs_vertical_slider, 0);
                        }

                        self.selectVerticalThumb(null, $thumbs, 0);
                        $thumbs.on('click', '.img-thumbnail', function() {
                            self.selectVerticalThumb($this, $thumbs, $(this).index());
                        });

                        $thumbs_slider.on('click', '.thumb-prev', function(e) {
                            var currentThumb = $thumbs_slider.data('currentThumb');
                            self.selectThumb($this, $thumbs_slider, --currentThumb);
                        });
                        $thumbs_slider.on('click', '.thumb-next', function(e) {
                            var currentThumb = $thumbs_slider.data('currentThumb');
                            self.selectThumb($this, $thumbs_slider, ++currentThumb);
                        });

                        if (theme.product_image_popup) {
                            var links = [], i = 0;
                            $this.find('img').each(function() {
                                var slide = {};

                                slide.src = $(this).attr('href');
                                slide.title = $(this).attr('alt');

                                links[i] = slide;
                                i++;
                            });
                        }

                        var itemsCount = typeof $this.data('items') != 'undefined' ? $this.data('items') : 1,
                            itemsResponsive = typeof $this.data('responsive') != 'undefined' ? $this.data('responsive') : {};
                        for (var itemCount in itemsResponsive) {
                            itemsResponsive[itemCount] = { items: itemsResponsive[itemCount] };
                        }

                        $this.owlCarousel({
                            rtl: theme.rtl,
                            loop : (count > 1) ? true : false,
                            autoplay : false,
                            items : itemsCount,
                            responsive: itemsResponsive,
                            autoHeight : true,
                            nav: true,
                            navText: ["", ""],
                            dots: false,
                            rewind: true,
                            lazyLoad: true,
                            onInitialized : function() {
                                if (theme.product_zoom && (!('ontouchstart' in document) || (('ontouchstart' in document) && theme.product_zoom_mobile))) {
                                    $this.find('img').each(function() {
                                        var $this = $(this);
                                        zoomConfig.zoomContainer = $this.parent();
                                        $this.elevateZoom(zoomConfig);
                                    });
                                }
                            },
                            onTranslate : function(event) {
                                currentSlide = event.item.index - $this.find('.cloned').length / 2;
                                self.selectThumb(null, $thumbs_slider, currentSlide);
                                self.selectVerticalThumb(null, $thumbs, currentSlide);
                                self.selectVerticalSliderThumb(null, $thumbs_vertical_slider, currentSlide);

                                var $obj = event.relatedTarget.items(currentSlide).find('img.owl-lazy:not(.owl-lazy-loaded)');
                                if ($obj.length) {
                                    var src = $obj.attr('href'),
                                        elevateZoom = $obj.data('elevateZoom');
                                    if (typeof elevateZoom != 'undefined') {
                                        elevateZoom.swaptheimage(src, src);
                                    }
                                }
                            },
                            onRefreshed: function() {
                                if (theme.product_zoom && (!('ontouchstart' in document) || (('ontouchstart' in document) && theme.product_zoom_mobile))) {
                                    $this.find('img').each(function() {
                                        var $this = $(this),
                                            src = typeof $this.attr('href') != 'undefined' ? $this.attr('href') : $this.attr('src'),
                                            elevateZoom = $this.data('elevateZoom');
                                        if (typeof elevateZoom != 'undefined') {
                                            elevateZoom.startZoom();
                                            elevateZoom.swaptheimage(src, src);
                                        }
                                    });
                                }
                            }
                        });

                        $this.data('links', links);

                        if (theme.product_image_popup) {
                            var $zoom_buttons = $this.next();
                            $zoom_buttons.off('click').on('click', function(e) {
                                e.preventDefault();
                                var options = {index: currentSlide, event: e};
                                $.magnificPopup.close();
                                $.magnificPopup.open($.extend(true, {}, theme.mfpConfig, {
                                    items: $this.data('links'),
                                    gallery: {
                                        enabled: true
                                    },
                                    type: 'image'
                                }), currentSlide);
                            });
                        }
                    });
                });

                return self;
            },

            selectThumb: function($image_slider, $thumbs_slider, index) {
                if (flag || !$thumbs_slider.length ) return;

                flag = true;
                var len = $thumbs_slider.find('.owl-item').length,
                    actives = [],
                    i = 0;

                index = (index + len) % len;
                if ($image_slider) {
                    $image_slider.trigger('to.owl.carousel', [index, duration, true]);
                }
                $thumbs_slider.find('.owl-item').removeClass('selected');
                $thumbs_slider.find('.owl-item:eq(' + index + ')').addClass('selected');
                $thumbs_slider.data('currentThumb', index);
                $thumbs_slider.find('.owl-item.active').each(function() {
                    actives[i++] = $(this).index();
                });
                if ($.inArray(index, actives) == -1) {
                    if (Math.abs(index - actives[0]) > Math.abs(index - actives[actives.length - 1])) {
                        $thumbs_slider.trigger('to.owl.carousel', [(index - actives.length + 1) % len, duration, true]);
                    } else {
                        $thumbs_slider.trigger('to.owl.carousel', [index % len, duration, true]);
                    }
                }
                flag = false;
            },

            selectVerticalSliderThumb: function($image_slider, $thumbs_vertical_slider, index) {
                if (flag || !$thumbs_vertical_slider.length ) return;
                flag = true;
                var len = $thumbs_vertical_slider[0].slick.slideCount,
                    actives = [],
                    i = 0;
                index = (index + len) % len;
                if ($image_slider) {
                    $image_slider.trigger('to.owl.carousel', [index, duration, true]);
                }
                $thumbs_vertical_slider.find('.img-thumbnail').removeClass('selected');
                $thumbs_vertical_slider.find('.img-thumbnail:eq(' + index + ')').addClass('selected');
                $thumbs_vertical_slider.data('currentThumb', index);
                $thumbs_vertical_slider.find('.img-thumbnail.slick-active').each(function() {
                    actives[i++] = $(this).index();
                });
                if ($.inArray(index, actives) == -1) {
                    if (Math.abs(index - actives[0]) > Math.abs(index - actives[actives.length - 1])) {
                        $thumbs_vertical_slider.get(0).slick.goTo((index - actives.length + 1) % len, false);
                    } else {
                        $thumbs_vertical_slider.get(0).slick.goTo(index % len, false);
                    }
                }
                flag = false;
            },

            selectVerticalThumb: function($image_slider, $thumbs, index) {
                if (flag || !$thumbs.length ) return;
                flag = true;
                var len = $thumbs.find('.img-thumbnail').length,
                    i = 0;

                index = (index + len) % len;
                if ($image_slider) {
                    $image_slider.trigger('to.owl.carousel', [index, duration, true]);
                }
                $thumbs.find('.img-thumbnail').removeClass('selected');
                $thumbs.find('.img-thumbnail:eq(' + index + ')').addClass('selected');
                $thumbs.data('currentThumb', index);
                flag = false;
            }
        }

    });

}).apply(this, [window.theme, jQuery]);


// Woocommerce Quick View
(function(theme, $) {

    theme = theme || {};

    $.extend(theme, {

        WooQuickView: {

            initialize: function() {

                this.events();

                return this;
            },

            events: function() {
                var self = this;

                $(document).on('click', '.quickview', function(e) {
                    e.preventDefault();

                    var pid = $(this).attr('data-id');

                    $.fancybox({
                        href : theme.ajax_url,
                        ajax : {
                            data: {
                                action: 'porto_product_quickview',
                                pid: pid
                            }
                        },
                        type : 'ajax',
                        helpers : {
                            overlay: {
                                locked: true,
                                fixed: true
                            }
                        },
                        tpl: {
                            error    : '<p class="fancybox-error">' + theme.request_error + '</p>',
                            closeBtn : '<a title="' + js_porto_vars.popup_close + '" class="fancybox-item fancybox-close" href="javascript:;"></a>',
                            next     : '<a title="' + js_porto_vars.popup_next + '" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
                            prev     : '<a title="' + js_porto_vars.popup_prev + '" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
                        },
                        autoSize: true,
                        autoWidth : true,
                        afterShow : function() {
                            setTimeout(function() {
                                porto_woocommerce_init();
                                theme.WooProductImageSlider.initialize($('.quickview-wrap-' + pid).find('.product-image-slider'));
                                    // Variation Form
                                var form_variation = $('.quickview-wrap-' + pid).find('form.variations_form');
                                if (form_variation.length > 0) {
                                    form_variation.wc_variation_form();
                                    form_variation.find("select option:selected").removeAttr("selected");
                                }
                            }, 200);
                        },
                        onUpdate : function() {
                            setTimeout(function() {
                                porto_woocommerce_init();
                                var $slider = $('.quickview-wrap-' + pid).find('.product-image-slider');
                                if (typeof $slider.data('owl.carousel') != 'undefined' && typeof $slider.data('owl.carousel')._invalidated != 'undefined')
                                    $slider.data('owl.carousel')._invalidated.width = true;
                                $slider.trigger('refresh.owl.carousel');
                            }, 300);
                        }
                    });
                    return false;
                });

                return self;
            }
        }

    });

}).apply(this, [window.theme, jQuery]);


// Woocommerce Qty Field
(function(theme, $) {

    theme = theme || {};

    $.extend(theme, {

        WooQtyField: {

            initialize: function() {

                this.build()
                    .events();

                return this;
            },

            build: function() {
                var self = this;

                // Quantity buttons
                $( 'div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<button type="button" value="+" class="plus">+</button>' ).prepend( '<button type="button" value="-" class="minus">-</button>' );

                // Target quantity inputs on product pages
                $( 'input.qty:not(.product-quantity input.qty)' ).each( function() {
                    var min = parseFloat( $( this ).attr( 'min' ) );

                    if ( min && min > 0 && parseFloat( $( this ).val() ) < min ) {
                        $( this ).val( min );
                    }
                });

                $( document ).off('click', '.plus, .minus').on( 'click', '.plus, .minus', function() {

                    // Get values
                    var $qty        = $( this ).closest( '.quantity' ).find( '.qty' ),
                        currentVal  = parseFloat( $qty.val() ),
                        max         = parseFloat( $qty.attr( 'max' ) ),
                        min         = parseFloat( $qty.attr( 'min' ) ),
                        step        = $qty.attr( 'step' );

                    // Format values
                    if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
                    if ( max === '' || max === 'NaN' ) max = '';
                    if ( min === '' || min === 'NaN' ) min = 0;
                    if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = 1;

                    // Change the value
                    if ( $( this ).is( '.plus' ) ) {

                        if ( max && ( max == currentVal || currentVal > max ) ) {
                            $qty.val( max );
                        } else {
                            $qty.val( currentVal + parseFloat( step ) );
                        }

                    } else {

                        if ( min && ( min == currentVal || currentVal < min ) ) {
                            $qty.val( min );
                        } else if ( currentVal > 0 ) {
                            $qty.val( currentVal - parseFloat( step ) );
                        }

                    }

                    // Trigger change event
                    $qty.trigger( 'change' );
                });

                return self;
            },

            events: function() {
                var self = this;

                $(document).ajaxComplete(function(event, xhr, options) {
                    self.build();
                });

                return self;
            }
        }

    });

}).apply(this, [window.theme, jQuery]);


// Woocommerce Variation Form
(function(theme, $) {

    theme = theme || {};

    var duration = 300;

    $.extend(theme, {

        WooVariationForm: {

            initialize: function() {

                this.events();

                return this;
            },

            events: function() {
                var self = this;

                $('.variations_form').each(function() {
                    var $variation_form = $( this ),
                        $reset_variations = $variation_form.find( '.reset_variations' );

                    if ($reset_variations.css('visibility') == 'hidden')
                        $reset_variations.hide();
                });

                $( document ).on( 'check_variations', '.variations_form', function( event, exclude, focus ) {
                    var $variation_form = $( this ),
                        $reset_variations = $variation_form.find( '.reset_variations' );

                    if ($reset_variations.css('visibility') == 'hidden')
                        $reset_variations.hide();
                });

                $( document ).on( 'reset_image', '.variations_form', function(event) {
                    var $product        = $(this).closest( '.product' );
                    var $product_img    = $product.find( 'div.product-images .woocommerce-main-image' );
                    var o_src           = $product_img.attr('data-o_src');
                    var o_title         = $product_img.attr('data-o_title');
                    var o_href          = $product_img.attr('data-o_href');
                    var $thumb_img      = $product.find( '.woocommerce-main-thumb' );
                    var o_thumb_src     = $thumb_img.attr('data-o_src');

                    var $image_slider = $product.find('.product-image-slider');
                    var $thumbs_slider = $product.find('.product-thumbs-slider');

                    if ($image_slider.length) {
                        $image_slider.trigger('to.owl.carousel', [0, duration, true]);
                    }
                    if ($thumbs_slider.length) {
                        $thumbs_slider.trigger('to.owl.carousel', [0, duration, true]);
                        $thumbs_slider.find('.owl-item:eq(0)').click();
                    }

                    var links = $image_slider.data('links');

                    if ( o_src ) {
                        $product_img
                            .attr( 'src', o_src )
                            .attr( 'srcset', '' )
                            .attr( 'alt', o_title )
                            .attr( 'href', o_href );

                        $product_img.each(function() {
                            var elevateZoom = $(this).data('elevateZoom');
                            if (typeof elevateZoom != 'undefined') {
                                elevateZoom.swaptheimage($(this).attr( 'src' ), $(this).attr( 'src' ));
                            }
                        });

                        if (typeof links != 'undefined') {
                            links[0].src = o_href;
                            links[0].title = o_title;
                        }
                    }
                    if (o_thumb_src) {
                        $thumb_img.attr( 'src', o_thumb_src );
                    }
                });

                $( document ).on( 'found_variation', '.variations_form', function(event, variation) {

                    if (typeof variation == 'undefined') {
                        return;
                    }

                    var $product              = $(this).closest( '.product' );

                    var $image_slider = $product.find('.product-image-slider');
                    var $thumbs_slider = $product.find('.product-thumbs-slider');

                    if ($image_slider.length) {
                        $image_slider.trigger('to.owl.carousel', [0, duration, true]);
                    }
                    if ($thumbs_slider.length) {
                        $thumbs_slider.trigger('to.owl.carousel', [0, duration, true]);
                        $thumbs_slider.find('.owl-item:eq(0)').click();
                    }

                    var links = $image_slider.data('links');

                    var $shop_single_image    = $product.find( 'div.product-images .woocommerce-main-image' );
                    var productimage           =  $shop_single_image.attr('data-o_src');
                    var imagetitle             =  $shop_single_image.attr('data-o_title');
                    var imagehref              =  $shop_single_image.attr('data-o_href');

                    var $shop_thumb_image = $product.find( '.woocommerce-main-thumb');
                    var thumbimage   =  $shop_thumb_image.attr('data-o_src');

                    var variation_image = variation.image_src;
                    var variation_link = variation.image_link;
                    var variation_title = variation.image_title;
                    var variation_thumb = variation.image_thumb;

                    if ( ! productimage ) {
                        productimage = $shop_single_image.attr('data-original') ? $shop_single_image.attr('data-original') : ( ( ! $shop_single_image.attr('src') ) ? '' : $shop_single_image.attr('src') );
                        $shop_single_image.attr('data-o_src', productimage );
                    }

                    if ( ! imagehref ) {
                        imagehref = ( ! $shop_single_image.attr('href') ) ? '' : $shop_single_image.attr('href');
                        $shop_single_image.attr('data-o_href', imagehref );
                    }

                    if ( ! imagetitle ) {
                        imagetitle = ( ! $shop_single_image.attr('alt') ) ? '' : $shop_single_image.attr('alt');
                        $shop_single_image.attr('data-o_title', imagetitle );
                    }

                    if ( ! thumbimage ) {
                        thumbimage = $shop_thumb_image.attr('data-original') ? $shop_thumb_image.attr('data-original') : ( ( ! $shop_thumb_image.attr('src') ) ? '' : $shop_thumb_image.attr('src') );
                        $shop_thumb_image.attr('data-o_src', thumbimage );
                    }

                    if ( variation_image ) {
                        $shop_single_image.attr( 'src', variation_image )
                        $shop_single_image.attr( 'srcset', '' )
                        $shop_single_image.attr( 'alt', variation_title )
                        $shop_single_image.attr( 'href', variation_link );
                        $shop_thumb_image.attr( 'src', variation_thumb );
                        if (typeof links != 'undefined') {
                            links[0].src = variation_link;
                            links[0].title = variation_title;
                        }
                    } else {
                        $shop_single_image.attr( 'src', productimage )
                        $shop_single_image.attr( 'srcset', '' )
                        $shop_single_image.attr( 'alt', imagetitle )
                        $shop_single_image.attr( 'href', imagehref );
                        $shop_thumb_image.attr( 'src', thumbimage );
                        if (typeof links != 'undefined') {
                            links[0].src = imagehref;
                            links[0].title = imagetitle;
                        }
                    }
                    $shop_single_image.each(function() {
                        var elevateZoom = $(this).data('elevateZoom');
                        if (typeof elevateZoom != 'undefined') {
                            elevateZoom.swaptheimage($(this).attr( 'src' ), $(this).attr( 'src' ));
                        }
                    });
                });

                return self;
            }
        }

    });

}).apply(this, [window.theme, jQuery]);


// Woocommerce Events
(function(theme, $) {

    theme = theme || {};

    $.extend(theme, {

        WooEvents: {

            initialize: function() {

                this.events();

                return this;
            },

            events: function() {
                var self = this;

                // wcml currency switcher
                $('.wcml-switcher li').on('click', function(){
                    if ($(this).parent().attr('disabled') == 'disabled')
                        return;
                    var currency = $(this).attr('rel');
                    self.loadCurrency(currency);
                });

                // woocommerce currency switcher
                $('.woocs-switcher li').on('click', function(){
                    if ($(this).parent().attr('disabled') == 'disabled')
                        return;
                    var currency = $(this).attr('rel');
                    self.loadWoocsCurrency(currency);
                });

                return self;
            },

            loadCurrency : function(currency) {
                $('.wcml-switcher').attr('disabled', 'disabled');
                $('.wcml-switcher').append('<li class="loading"></li>');
                var data = {action: 'wcml_switch_currency', currency: currency}
                $.ajax({
                    type : 'post',
                    url : theme.ajax_url,
                    data : {
                        action: 'wcml_switch_currency',
                        currency : currency
                    },
                    success: function(response) {
                        $('.wcml-switcher').removeAttr('disabled');
                        $('.wcml-switcher').find('.loading').remove();
                        window.location = window.location.href;
                    }
                });
            },

            loadWoocsCurrency : function(currency) {
                $('.woocs-switcher').attr('disabled', 'disabled');
                $('.woocs-switcher').append('<li class="loading"></li>');
                var l = window.location.href;
                l = l.split('?');
                l = l[0];
                var string_of_get = '?';
                woocs_array_of_get.currency = currency;
                
                if (Object.keys(woocs_array_of_get).length > 0) {
                    jQuery.each(woocs_array_of_get, function (index, value) {
                        string_of_get = string_of_get + "&" + index + "=" + value;
                    });
                }
                window.location = l + string_of_get;
            },

            removeParameterFromUrl : function(url, parameter) {
                return url
                    .replace(new RegExp('[?&]' + parameter + '=[^&#]*(#.*)?$'), '$1')
                    .replace(new RegExp('([?&])' + parameter + '=[^&]*&'), '$1');
            }
        }

    });

}).apply(this, [window.theme, jQuery]);

function porto_woocommerce_init() {
    // Woo Widget Toggle
    (function($) {

        'use strict';

        if ($.isFunction($.fn['themeWooWidgetToggle'])) {

            $(function() {
                $('.widget_product_categories, .widget_price_filter, .widget_layered_nav, .widget_layered_nav_filters, .widget_rating_filter, .porto_widget_price_filter').find('.widget-title').each(function() {
                    var $this = $(this),
                        opts;

                    var pluginOptions = $this.data('plugin-options');
                    if (pluginOptions)
                        opts = pluginOptions;

                    $this.themeWooWidgetToggle(opts);
                });
            });

        }

    }).apply(this, [jQuery]);

    // Woo Widget Accordion
    (function($) {

        'use strict';

        if ($.isFunction($.fn['themeWooWidgetAccordion'])) {

            $(function() {
                $('.widget_product_categories, .widget_price_filter, .widget_layered_nav, .widget_layered_nav_filters, .widget_rating_filter').each(function() {
                    var $this = $(this),
                        opts;

                    var pluginOptions = $this.data('plugin-options');
                    if (pluginOptions)
                        opts = pluginOptions;

                    $this.themeWooWidgetAccordion(opts);
                });
            });

        }

    }).apply(this, [jQuery]);

    // Woo Products Slider
    (function($) {

        'use strict';

        if ($.isFunction($.fn['themeWooProductsSlider'])) {

            $(function() {
                $('.products-slider:not(.manual)').each(function() {
                    var $this = $(this),
                        opts;

                    var pluginOptions = $this.data('plugin-options');
                    if (pluginOptions)
                        opts = pluginOptions;

                    $this.themeWooProductsSlider(opts);
                });
            });

        }

    }).apply(this, [jQuery]);

    // Woocommerce Grid/List Toggle
    (function($) {

        'use strict';

        if ($.cookie && $.cookie('gridcookie')) {
            var $toggle = $('.gridlist-toggle');
            if ($toggle.get(0)) {
                var $parent = $toggle.parent().parent();
                if ($parent.find('ul.products').hasClass('grid')) {
                    $.cookie('gridcookie', 'grid', { path: '/' });
                } else if ($parent.find('ul.products').hasClass('list')) {
                    $.cookie('gridcookie', 'list', { path: '/' });
                } else {
                    $parent.find('ul.products').addClass($.cookie('gridcookie'));
                }
            }
        }

        if ($.cookie && $.cookie('gridcookie') == 'grid') {
            $('.gridlist-toggle #grid').addClass('active');
            $('.gridlist-toggle #list').removeClass('active');
            $('ul.products.grid > li.show-outimage-q-onimage .product-inner, ul.products.grid > li.show-links-outimage .product-inner, ul.products.grid > li.show-outimage-q-onimage-alt .product-inner').each(function(){
                $(this).children('.product-loop-title').before($(this).children('.rating-wrap'));
            });
        }

        if ($.cookie && $.cookie('gridcookie') == 'list') {
            $('.gridlist-toggle #list').addClass('active');
            $('.gridlist-toggle #grid').removeClass('active');
            $('ul.products.list > li.show-outimage-q-onimage .product-inner, ul.products.list > li.show-links-outimage .product-inner, ul.products.list > li.show-outimage-q-onimage-alt .product-inner').each(function(){
                $(this).children('.product-loop-title').after($(this).children('.rating-wrap'));
            });
        }

        if ($.cookie && $.cookie('gridcookie') == null) {
            var $toggle = $('.gridlist-toggle');
            if ($toggle.get(0)) {
                var $parent = $toggle.parent().parent();
                $parent.find('ul.products').addClass('grid');
            }
            $('.gridlist-toggle #grid').addClass('active');
            if ($.cookie)
                $.cookie('gridcookie', 'grid', { path: '/' });
        }

    }).apply(this, [jQuery]);
}

(function(theme, $) {

    'use strict';

    $(document).ready(function() {
        // Woocommerce Products Infinite
        if (typeof theme.ProductsInfinite !== 'undefined') {
            theme.ProductsInfinite.initialize();
        }

        // Woocommerce Qty Field
        if (typeof theme.WooQtyField !== 'undefined') {
            theme.WooQtyField.initialize();
        }

        // Woocommerce Quick View
        if (typeof theme.WooQuickView !== 'undefined') {
            theme.WooQuickView.initialize();
        }

        // Woocommerce Events
        if (typeof theme.WooEvents !== 'undefined') {
            theme.WooEvents.initialize();
        }

        // disable drop down
        if (!('ontouchstart' in document)) {
            $('.mini-cart').on('hide.bs.dropdown', function () {
                return false;
            });
        }

        $(document).on('tabactivate', '.woocommerce-tabs', function(e, ui) {
            var label = $(ui).attr('aria-controls');
            var panel = $('[aria-labelledby="' + label + '"');
            theme.refreshVCContent(panel);
        });
    });
}).apply(this, [window.theme, jQuery]);


(function (theme, $, undefined) {
    "use strict";

    // Woocommerce Variation Form
    if (typeof theme.WooVariationForm !== 'undefined') {
        theme.WooVariationForm.initialize();
    }

    // Woocommerce Product Image Slider
    if (typeof theme.WooProductImageSlider !== 'undefined') {
        theme.WooProductImageSlider.initialize();
    }

    $(document).ready(function(){
        porto_woocommerce_init();

        $(window).bind('vc_reload', function() {
            porto_woocommerce_init();
            $('.type-product').addClass('product');
        });

        // shop horizontal filter
        $(document).on('click', '.porto-product-filters-toggle', function(e) {
            e.preventDefault();
            $(this).toggleClass('opened');
            $(this).siblings('.porto-product-filters-body').toggle();
            return false;
        });
        $(document).on('click', '.porto-product-filters.style2 .widget-title', function(e) {
            e.preventDefault();
            if ($(this).next().is(':hidden')) {
                $('.porto-product-filters.style2 .widget-title').next().hide();
                $('.porto-product-filters.style2 .widget').removeClass('opened');
                $(this).next().show();
            } else {
                $(this).next().hide();
            }
            $(this).parent().toggleClass('opened');
            return false;
        });
        $('body').on('click', function(e) {
            if (!$(e.target).is('.porto-product-filters') && !$(e.target).is('.porto-product-filters *')) {
                $('.porto-product-filters:not(.style2) .porto-product-filters-body').hide();
                $('.porto-product-filters:not(.style2) .porto-product-filters-toggle').removeClass('opened');
                $('.porto-product-filters.style2 .widget-title').next().hide();
                $('.porto-product-filters.style2 .widget').removeClass('opened');
            }
        });
    });

    $('.cart-v2 .cart_totals .accordion-toggle.out').removeClass('out');
    $(document).ajaxComplete(function(event, xhr, options) {
        $('.cart-v2 .cart_totals .accordion-toggle.out').each(function(){
            if($($(this).attr('href')).length && $($(this).attr('href')).is(':hidden')) {
                $(this).removeClass('collapsed');
                $($(this).attr('href')).addClass('show');
            }
        });
    });
})( window.theme, jQuery );