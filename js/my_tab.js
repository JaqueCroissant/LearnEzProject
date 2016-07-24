var current_tab;
var clicked = true;
var ready_to_change = true;

$(document).ready(function () {
    var page_step = $.cookie("current_page_step") !== undefined ? $.cookie("current_page_step") : undefined;
    if (page_step !== undefined) {
        current_tab = "#" + page_step + "_tab";
        $("#" + page_step + "_a").parent().removeClass("hidden");
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
        $(this).parent().removeClass("hidden");
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
            $(this).children().removeClass("link_disabled");
        });
    });
});