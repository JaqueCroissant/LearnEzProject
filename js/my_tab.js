var current_tab;

$(document).ready(function () {
    var page_args = getURLParameters(location.href);
    if (typeof page_args['step'] !== 'undefined') {
        current_tab = "#" + page_args['step'];
    } else {
        current_tab = "#" + $(".my_tab").first().attr("id");
    }
    $(current_tab).addClass("in");
    
    $(document).on("click", ".my_tab_header", function (event) {
        event.preventDefault();
        var tab = $(this).attr("href");

        if (tab !== (current_tab)) {
            $(".my_tab").each(function (e) {
                if ($(this).hasClass("in")) {
                    $(this).removeClass("in", 350);
                }
            });
            $(tab).addClass("in", 350);
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