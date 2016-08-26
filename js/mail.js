
$(document).on("click", ".assign_mail_folder", function (event) {
    event.preventDefault();
    var current_page = $(this).attr("current_folder") === "inbox" ? "" : $(this).attr("current_folder");
    if ($(this).attr("mail_id") !== undefined && $(this).attr("step") !== undefined && $(this).attr("current_folder") !== undefined) {
        submit_get("mail.php?step=" + $(this).attr("step") + "&mail_id=" + $(this).attr("mail_id") + "&current_folder=" + $(this).attr("current_folder"), $(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            change_page("mail", current_page);
        });
    } else {
        if ($("#" + $(this).attr("target_form") + " input:checkbox:checked").length > 0) {
            initiate_custom_submit_form($(this), function () {
                show_status_bar("error", ajax_data.error);
            }, function () {
                if (ajax_data.mails_removed !== undefined) {
                    ajax_data.mails_removed.forEach(function (entry) {
                        $(".mail_number_" + entry).fadeOut(500, function () {
                            $(this).remove();
                        });
                    });
                }
            }, $(this).attr("args"), $(this).attr("target_form"));
        }
    }

});

$(document).on("click", ".submit_create_mail", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        change_page("mail")
        show_status_bar("success", ajax_data.success);
    });
});

$(document).on("click", ".submit_search_mail", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        change_page("mail", "search", ajax_data.url);
        //show_status_bar("success", ajax_data.success);
    });
});

$(document).on("click", ".submit_reply_mail", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        $(".reply_form").fadeOut(500);
        show_status_bar("success", ajax_data.success);
    });
});