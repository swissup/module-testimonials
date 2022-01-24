(function () {
    'use strict';

    $.widget('testimonialsList', {
        component: 'Swissup_Testimonials/js/list',

        /** [create description] */
        create: function () {
            $.testimonialsList(this.options, this.element);
        }
    });
})();
