$(document).ready(function ($) {
    if (window.history && window.history.pushState) {
        window.history.pushState('forward', null, './#forward');
        $(window).on('popstate', function () {
            if ($.cookie("navigation") !== undefined) {
                var navigation = $.map(JSON.parse($.cookie("navigation")), function (value, index) {
                    return [value];
                });

                if ((navigation.length < 2 && !is_error_page) || (navigation.length < 1 && is_error_page)) {
                    return;
                }

                var last_page = navigation.pop();
                if (is_error_page !== true) {
                    last_page = navigation.pop();
                }

                $.cookie("navigation", JSON.stringify(navigation), {expires: 10, path: '/'});
                change_page(last_page.page, last_page.step, last_page.args);
            }
        });

    }
});