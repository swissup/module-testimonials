define(["jquery"], function($) {
    var self,
        curTestimonial = 0,
        showMoreActive = false,
        changeInterval,
        contentHeight,
        numTestimonials,
        viewTime,
        changeAnimDuration;

    return {
        init: function(num, showTime, animDuration) {
            self = this;
            numTestimonials = num;
            viewTime = showTime;
            changeAnimDuration = animDuration;
            contentHeight = $('.block-testimonials .block-content .content .content-wrapper').height();

            if (numTestimonials > 1) {
                self.startChangeTimer();
                $('#testimonialsList').on('mouseenter', function() {
                    if (!showMoreActive) clearInterval(changeInterval);
                });
                $('#testimonialsList').on('mouseleave', self.startChangeTimer);
            }
            if ($('#testimonial_0 .read-more')) {
                $('#testimonial_0 .read-more').on('click', self.showMore);
                $('#testimonial_0 .read-less').on('click', self.showLess);
            }
        },
        showMore: function() {
            $this = $(this);
            showMoreActive = true;
            $this.hide();
            $this.parent().find('.read-less').show();
            $this.parent().find('.content-wrapper').height('auto');
            return false;
        },
        showLess: function() {
            $this = $(this);
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
            if ($('#testimonial_0 .read-more')) {
                $('#testimonial_' + curTestimonial + ' .read-more').off('click');
                $('#testimonial_' + curTestimonial + ' .read-less').off('click');
            }
            $('#testimonial_' + curTestimonial).fadeOut(changeAnimDuration, function() {
                ++curTestimonial;
                if (curTestimonial >= numTestimonials) curTestimonial = 0;
                $('#testimonial_' + curTestimonial).fadeIn(changeAnimDuration);
                if ($('#testimonial_0 .read-more')) {
                    $('#testimonial_' + curTestimonial + ' .read-more').on('click', self.showMore);
                    $('#testimonial_' + curTestimonial + ' .read-less').on('click', self.showLess);
                }
            });
        }
    }
});