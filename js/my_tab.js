var current_tab;

$(document).ready(function () {
    if($.cookie("navigation") !== undefined) {
        var navigation = $.map(JSON.parse($.cookie("navigation")), function(value, index) {
            return [value];
        });

        if(navigation.length < 1) {
            return;
        }

        var last_page = navigation.pop().step;
    }
    if (last_page !== undefined && last_page !== "") {
        current_tab = "#" + last_page + "_tab";
        $("#" + last_page + "_a").parent().removeClass("hidden");
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