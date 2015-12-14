define(["jquery"], function($) {
    var url,
        div,
        currentPage = 1;

    return {
        init: function(ajaxCallUrl, divToUpdate) {
            url = ajaxCallUrl;
            div = $(divToUpdate);
        },
        makeAjaxCall: function(event) {
            if ($('.more-button a').hasClass('disabled')) return;

            $('.more-button a').addClass('disabled');
            ++currentPage;
            $.post(url, { page: currentPage },
                function(data) {
                    div.append(data.outputHtml);
                    $('.more-button a').removeClass('disabled');
                },
                'json'
            );

            return false;
        }
    }
});