(function (factory) {
    'use strict';

    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else {
        $.testimonialsSideList = factory($);
    }
}(function ($) {
    'use strict';

    var self,
        curTestimonial = 0,
        showMoreActive = false,
        changeInterval,
        contentHeight,
        numTestimonials,
        viewTime,
        changeAnimDuration;

    return {
        'Swissup_Testimonials/js/side-list-widget': function (config, element) {
            this.element = $(element);
            this.options = config;
            this.init();
        },
        init: function() {
            self = this;
            numTestimonials = this.element.find('.testimonial-container > div').length;
            viewTime = this.options.viewTime;
            changeAnimDuration = this.options.animDuration;
            contentHeight = this.element.find('.content-wrapper').height();

            if (numTestimonials > 1) {
                self.startChangeTimer();
                $('#testimonialsList').on('mouseenter', function() {
                    if (!showMoreActive) clearInterval(changeInterval);
                });
                $('#testimonialsList').on('mouseleave', self.startChangeTimer);
            }

            this.element.find('.read-more').on('click', self.showMore);
            this.element.find('.read-less').on('click', self.showLess);
        },
        showMore: function() {
            var $this = $(this);

            showMoreActive = true;
            $this.hide();
            $this.parent().find('.read-less').show();
            $this.parent().find('.content-wrapper').height('auto');
            return false;
        },
        showLess: function() {
            var $this = $(this);

            showMoreActive = false;
            $this.hide();
            $this.parent().find('.read-more').show();
            $this.parent().find('.content-wrapper').height(contentHeight);
            return false;
        },
        startChangeTimer: function() {
            if (!showMoreActive) {
                changeInterval = setInterval(self.nextTestimonial, viewTime);
            }
        },
        nextTestimonial: function() {
            $('#testimonial_' + curTestimonial).fadeOut(changeAnimDuration);

            if (++curTestimonial >= numTestimonials) {
                curTestimonial = 0;
            }

            $('#testimonial_' + curTestimonial).fadeIn(changeAnimDuration);
        }
    };
}));
