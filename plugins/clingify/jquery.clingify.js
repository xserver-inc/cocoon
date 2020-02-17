/*
 * Clingify v1.2
 *
 * A jQuery 1.7+ plugin for sticky elements
 * http://github.com/theroux/clingify
 *
 * MIT License
 *
 * By Andrew Theroux
 */
// ';' protects against concatenated scripts which may not be closed properly.
;(function($, window, document, undefined) {
    'use strict';

    // defaults
    var pluginName = 'clingify',
        // the name of using in .data()
        dataPlugin = "plugin_" + pluginName,
        defaults = {
            breakpointHeight: 0,
            breakpointWidth: 0,
            // Media query breakpoints in pixels. 
            // Below this value, Clingify behavior is disabled. (Useful for small screens.)
            // Use 0 if you want Clingify to work at all screen widths/heights.

            throttle: 50,
            // Delay Clingify functions, in milliseconds, when scrolling/resizing.
            // Too fast is bad for performance, especially on older browsers/machines.

            extraClass: '',
            // Add an additional class of your choosing to the sticky element 
            // and its parent wrapper & placeholder divs

            // Classes for CSS hooks
            wrapperClass: 'js-clingify-wrapper',
            lockedClass: 'js-clingify-locked',
            overrideClass: 'js-clingify-permalock',
            placeholderClass: 'js-clingify-placeholder',

            // Callback functions
            detached: $.noop, // Fires before element is detached
            locked: $.noop, // Fires before element is attached
            resized: $.noop, // Fires after window resize event, benefits from the throttle
        
            //new 
            scrollingElem : 'window',
            fixed: true, // Use fixed positioning vs. transforms applied to elem    

        },
        $window = $(window);

    /* 
    var privateMethod = function () {
        console.log("private methods go here!");
    }; 
    */

    // The actual plugin constructor
    var Plugin = function ( element ) {
        // Plugin instantiation
        
        this.element = element;
        this.options = $.extend( {}, defaults );
    };

    Plugin.prototype = {

        init: function ( options ) {

            // extend options ( http://api.jquery.com/jQuery.extend/ )
            $.extend( this.options, options );
            var cling = this,
                $elem = $(this.element),
                scrollTimeout,
                throttle = this.options.throttle;

            this.wrap();

            if ((this.options.extraClass !== "") && (typeof this.options.extraClass === "string")) {
                this.findWrapper().addClass(this.options.extraClass);
                this.findPlaceholder().addClass(this.options.extraClass);
                this.options.wrapperClass += "." + this.options.extraClass;
                this.options.placeholderClass += "." + this.options.extraClass;
            }

            if (this.options.scrollingElem === "window") {
                this.options.scrollingElem = $(window);
            } else {
                this.options.scrollingElem = $(this.options.scrollingElem);
            }

            this.bindScroll();
            this.bindResize();
        },

        bindResize: function() {
            var cling = this,
                scrollTimeout;

            $(window).on('resize.Clingify', function(event) {
                if (!scrollTimeout) {
                    scrollTimeout = setTimeout(function() {
                        if ((event.type === 'resize') && (typeof cling.options.resized === 'function')) {
                            cling.options.resized();
                        }
                        cling.checkElemStatus();
                        scrollTimeout = null;
                    }, cling.options.throttle);
                }
            });
        },

        bindScroll: function() {
            var cling = this,
                scrollTimeout;

            $(cling.options.scrollingElem).on('scroll.Clingify', function(event) {
                if (!scrollTimeout) {
                    scrollTimeout = setTimeout(function() {
                        cling.checkElemStatus();
                        scrollTimeout = null;
                    }, cling.options.throttle);
                }
            });
        },

        unbindResize: function() {
            $(window).off('resize.Clingify');
        },        
        unbindScroll: function() {
            $(this.options.scrollingElem).off('scroll.Clingify');
        },

        destroy: function () {
            this.unwrap();

            // unset Plugin data instance
            this.element.removeData(dataPlugin);
            return;
        },

        //Other functions below
        checkCoords: function() {
            var coords = {
                    windowHeight: $(this.options.scrollingElem).height(),
                    windowWidth: $(this.options.scrollingElem).width(),
                    windowOffset: $(this.options.scrollingElem).scrollTop(),
                    // Y-position for Clingify placeholder
                    // needs to be recalculated in DOM has shifted
                    placeholderOffset: this.findPlaceholder().offset().top
                };
            return coords;
        },

        detachElem: function() {
            if (typeof this.options.detached === 'function') {
                this.options.detached(); // fire callback
            }
            if (this.findWrapper().hasClass(this.options.overrideClass)) {
                return;
            } else {
                this.findWrapper().removeClass(this.options.lockedClass);
            }
            return;
        },

        lockElem: function() {
            if (typeof this.options.locked === 'function') {
                this.options.locked(); // fire callback
            }
            this.findWrapper().addClass(this.options.lockedClass);
            return;
        },

        findPlaceholder: function() {
            return this.$element.closest('.'+ this.options.placeholderClass);
        },

        findWrapper: function() {
            return this.$element.closest('.'+ this.options.wrapperClass);
        },

        checkElemStatus: function() {
            var cling = this,
                currentCoords = this.checkCoords(),
                fixed = this.options.fixed,
                isScrolledPast = function() {
                    if (currentCoords.windowOffset >= currentCoords.placeholderOffset) {
                        return true;
                    } else {
                        return false;
                    }
                },
                isWideTallEnough = function() {
                    if ((currentCoords.windowWidth >= cling.options.breakpointWidth) && currentCoords.windowHeight >= cling.options.breakpointHeight) {
                        return true;
                    } else {
                        return false;
                    }
                },
                distanceScrolled = function() {
                    return ( cling.options.scrollingElem.scrollTop() );
                };

            if ( (isScrolledPast() && isWideTallEnough()) && fixed ) {
                this.lockElem();
            } else if ( (!isScrolledPast() || !isWideTallEnough()) && fixed ) {
                this.detachElem();
            } else if ( (isScrolledPast() && isWideTallEnough()) && !fixed ) {
                this.transformElem( distanceScrolled() );

            } else if ( (!isScrolledPast() || !isWideTallEnough()) && !fixed ) {
                this.untransformElem();
            }
            return;
        },

        unwrap: function() {
            // Removes wrapper and placeholder
            this.findPlaceholder().replaceWith(this.element);
            return;
        },
        test: function() {
            console.log('Public test method is working!');
        },
        transformElem: function(height) {
            var $wrapper = this.findWrapper(),
                transformAmount = height,
                transformString = "translateY(" + transformAmount + "px)";

            $wrapper.css({

                "transform": transformString
            });
            return;
        },
        untransformElem: function() {
            var resetTransform = "translateY(0)";
            this.findWrapper().css({
                "transform": resetTransform
            });
            return;
        },
        wrap: function() {
            // Creates wrapper and placeholder divs
            var $buildPlaceholder = $('<div>').addClass(this.options.placeholderClass),
                $buildWrapper = $('<div>').addClass(this.options.wrapperClass);

            this.$element = $(this.element);
            this.elemHeight = this.$element.outerHeight();

            this.$element
                .wrap($buildPlaceholder.height(this.elemHeight))
                .wrap($buildWrapper);
            this.findPlaceholder().height(this.elemHeight);
            return;
        }
    };
   
    $.fn[ pluginName ] = function ( arg ) {

        var args, instance;

        // only allow the plugin to be instantiated once
        if (!( this.data( dataPlugin ) instanceof Plugin )) {

            // if no instance, create one
            this.data( dataPlugin, new Plugin( this ) );
        }

        instance = this.data( dataPlugin );

        instance.element = this;

        // Is the first parameter an object (arg), or was omitted,
        // call Plugin.init( arg )
        if (typeof arg === 'undefined' || typeof arg === 'object') {

            if ( typeof instance['init'] === 'function' ) {
                instance.init( arg );
            }

        // checks that the requested public method exists
        } else if ( typeof arg === 'string' && typeof instance[arg] === 'function' ) {

            // copy arguments & remove function name
            args = Array.prototype.slice.call( arguments, 1 );

            // call the method
            return instance[arg].apply( instance, args );

        } else {

            $.error('Method ' + arg + ' does not exist on jQuery.' + pluginName);

        }
    };
})(jQuery, window, document);