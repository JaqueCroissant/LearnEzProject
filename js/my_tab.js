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
});