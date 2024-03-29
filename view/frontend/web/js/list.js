define(['jquery'], function ($) {
    'use strict';

    var result = function (config, element) {
        var currentPage = 1,
            viewMore = $('#viewMore');

        function makeAjaxCall() {
            if (viewMore.hasClass('disabled')) {
                return false;
            }

            viewMore.addClass('disabled');

            $.get({
                url: config.loadAction,
                dataType: 'json',
                data: {
                    page: ++currentPage
                },
                success: function (data) {
                    $(element).append(data.outputHtml);
                    viewMore.removeClass('disabled');

                    if (data.lastPage) {
                        viewMore.hide();
                    }
                }
            });

            return false;
        }

        if (viewMore) {
            viewMore.on('click', makeAjaxCall);
        }
    };

    result.component = 'Swissup_Testimonials/js/list';

    return result;
});
