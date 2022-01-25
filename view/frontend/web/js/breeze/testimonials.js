(function () {
    'use strict';

    $.widget('testimonialsList', {
        component: 'Swissup_Testimonials/js/list',

        /** [create description] */
        create: function () {
            $.testimonialsList(this.options, this.element);
        }
    });

    $.widget('testimonialsSideList', {
        component: 'Swissup_Testimonials/js/side-list-widget',

        /** [create description] */
        create: function () {
            $.testimonialsSideList['Swissup_Testimonials/js/side-list-widget'](this.options, this.element);
        }
    });
})();
