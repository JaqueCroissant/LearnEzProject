var current_tab;
var clicked = true;
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
        console.log($(this).attr("class"));
        if ($(this).hasClass("link_disabled")) {
            return;
        }
        $(this).closest("ul").children().each(function (e) {            
            $(this).children().addClass("link_disabled");
        });
        var tab = $(this).attr("href");
        $(".my_tab_header").each(function (e) {
            if ($(this).hasClass("my_active")) {
                $(this).removeClass("my_active");
            }
        });
        if (tab !== (current_tab)) {
            current_tab = tab;
            $(".my_tab").each(function (e) {
                if ($(this).hasClass("in")) {
                    $(this).removeClass("in");
                }
            });
            $(tab).addClass("in", 300);
            current_tab = tab;
        }
        $(this).closest("ul").children().each(function (e) {            
            console.log($(this).children().attr("class"));
            $(this).children().removeClass("link_disabled");
            
        });
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