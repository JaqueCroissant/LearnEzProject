var current_tab;
var ready_to_change = true;

$(document).ready(function () {
    var page_args = getURLParameters(location.href);
    if (typeof page_args['step'] !== 'undefined') {
        current_tab = "#" + page_args['step'];
    } else {
        current_tab = "#" + $(".my_tab").first().attr("id");
    }
    $(current_tab).addClass("in");
    $(".my_tab_header").each(function (e) {
        if ($(this).attr("href") === current_tab) {
            $(this).addClass("my_active");
        }
    });

    $(document).on("click", ".my_tab_header", function (event) {
        event.preventDefault();
        var tab = $(this).attr("href");
        $(".my_tab_header").each(function (e) {
            if ($(this).hasClass("my_active")) {
                $(this).removeClass("my_active");
            }
        });
        if (tab !== (current_tab)) {
            $(".my_tab").each(function (e) {
                if ($(this).hasClass("in")) {
                    $(this).removeClass("in");
                }
            });
            $(tab).addClass("in", 300);
            current_tab = tab;
        }
    });

    function getURLParameters(url) {

        var result = {};
        var searchIndex = url.indexOf("?");
        if (searchIndex === -1)
            return result;
        var sPageURL = url.substring(searchIndex + 1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++)
        {
            var sParameterName = sURLVariables[i].split('=');
            result[sParameterName[0]] = sParameterName[1];
        }
        return result;
    }
});