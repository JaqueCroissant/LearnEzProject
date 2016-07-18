var currently_changing_page = false;
var currently_submitting_form = false;
var currently_submitting_get = false;
var content_hidden = false;
var status_bar_timeout;

var clicked_checkbox_id;
var delete_class_id;
var school;
var ajax_data;

$(document).ready(function () {
    // Load on startup.
    initial_page_load();
    // Load on startup.


    // global functions
    $(document).on("click", ".change_page", function (event) {
        if (currently_changing_page === false && $(this).attr("clickable") !== "false" && !$(this).attr('disabled')) {
            $(this).attr("clickable", false);
            event.preventDefault();
            var page = $(this).attr("page");
            var step = $(this).attr("step");
            var args = $(this).attr("args");
            change_page(page, step, args, $(this));
        }
    });

    $(document).on("click", ".check_all", function (event) {
        event.preventDefault();
        var form = $(this).attr("target_form");
        var checkboxes = $("#" + form).find(':checkbox');
        if ($(this).attr("checked")) {
            checkboxes.prop('checked', false);
            $(this).removeAttr("checked");
            $(this).find("i").first().toggleClass('fa-square-o fa-check-square-o');
        } else {
            checkboxes.prop('checked', true);
            $(this).attr("checked", true);
            $(this).find("i").first().toggleClass('fa-check-square-o fa-square-o');
        }
    });

    $(document).on("change", ".check_all_specific", function (event) {
        event.preventDefault();
        var checkbox_id = $(this).attr("checkbox_id");
        var form = $(this).closest("form").attr("id");
        var checkboxes = $("#" + form).find('.master_check_box_' + checkbox_id);
        if ($(this).is(":checked")) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    });
    //

    // login / logout
    $(document).on("click", ".submit_login", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            reload_page();
        });
    });


    $(document).on("click", ".log_out", function (event) {
        event.preventDefault();
        initiate_submit_get($(this), "login.php?logout=true", function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            $.removeCookie("current_page");
            reload_page();
        });
    });
    //

    // mail
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
    //

    //notifications
    $(document).on("click", ".notifs_button", function (event) {
        event.preventDefault();
        var action = $(this).attr("action");
        if ($("#" + $(this).attr("target_form") + " input:checkbox:checked").length > 0) {
            initiate_custom_submit_form($(this), function () {
                alert(ajax_data.error);
            }, function () {
                if (ajax_data.status_value !== undefined) {
                    if (ajax_data.status_value === true) {
                        if (action === "delete") {
                            ajax_data.affected_notifs.forEach(function (o) {
                                $(".notif_count_" + o).fadeOut(500, function () {
                                    $(this).remove();
                                });
                            });
                        }
                        else if (action === "read") {
                            ajax_data.affected_notifs.forEach(function (o) {
                                $(".notif_count_" + o).addClass("notif_read");
                            });
                        }
                    }
                    else {
                        alert(ajax_data.error);
                    }
                }
            }, $(this).attr("args"), $(this).attr("target_form"));
        }
    });
    //

    $(document).on("click", ".submit_create_mail", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
        });
    });

    $(document).on("click", ".submit_reply_mail", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            $(".reply_form").fadeOut(500);
        });
    });
    //

    // rights
    $(document).on("click", ".submit_change_rights", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
        });
    });

    // edit user info
    $(document).on("click", ".submit_edit_user_info", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            $(".username").html(ajax_data.full_name);
            $(".current-avatar-image").attr("src", "assets/images/profile_images/" + ajax_data.avatar_id + ".png");
        });
    });

    $(document).on("click", ".avatar-hover", function (event) {
        event.preventDefault();

        var avatar_id = $(this).attr("avatar_id");
        if (avatar_id === undefined) {
            return;
        }
        $(".current-avatar").attr("src", "assets/images/profile_images/" + avatar_id + ".png");
        $(".input_avatar_id").val(avatar_id);

    });

    $(document).on("input", "input.input_change", function (event) {
        event.preventDefault();
        $(".user_full_name").html($(".input_firstname").val() + " " + $(".input_surname").val());
    });

    $(document).on("click", ".settings_submit_password", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
        });
    });

    $(document).on("click", ".create_submit_info", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
        });
    });


    $(document).on("click", ".create_submit_csv", function (event) {

        var formData = new FormData($('#create_import_form')[0]);
        $.ajax({
            url: 'include/ajax/create_account.php?step=2',
            type: 'POST',
            data: formData,
            async: false,
            complete: function (data) {
                alert(data)
            },
            cache: false,
            contentType: false,
            processData: false
        });

        event.preventDefault();

    });

    $(document).on("click", ".reset_pass_submit_email2", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            setTimeout(function () {
                location.reload();
            }, 500);
            show_status_bar("success", ajax_data.success);
        });
    });

    $(document).on("click", ".update_class", function (event) {
        event.preventDefault();
        if ($("#class_open").val() === "on") {
            $("#class_open_hidden").val(1);
        } else {
            $("#class_open_hidden").val(0);
        }

        $("#hidden_description").val($("#class_description").val());
        ajax_data = undefined;
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            setTimeout(function () {
                change_page("find_class")
            }, 300);
            show_status_bar("success", ajax_data.success);
        });
    });

    $(document).on("click", ".delete_class", function (event) {
        event.preventDefault();

        $("td input[type='checkbox']").attr("disabled", true);
        position = $(this).offset();
        height = $("#close_class_alert").height();
        $("#delete_class_alert").css("top", position["top"] - height - 20);
        $("#delete_class_alert").removeAttr("hidden");
        delete_class_id = $(this).attr("id");
    });

    $(document).on("click", "#accept_delete_class_btn", function (event) {
        event.preventDefault();

        div_id = $(this).closest("div .alert_panel").attr("id");
        initiate_submit_form($("#" + delete_class_id), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            $("#" + div_id).attr("hidden", true);
            setTimeout(function () {
                location.reload();
            }, 300);
            show_status_bar("success", ajax_data.success);
        });
    });

    $(document).on("click", "#cancel_delete_class_btn", function (event) {
        event.preventDefault();

        div_id = $(this).closest("div .alert_panel").attr("id");
        $("#" + div_id).attr("hidden", true);
    });

    $(document).on("click", ".btn_class_open", function (event) {
        event.preventDefault();

        $("td input[type='checkbox']").attr("disabled", true);
        position = $(this).offset();
        height = $("#close_class_alert").height();
        if ($(this).val() === "on") {
            $("#close_class_alert").css("top", position["top"] - height - 20);
            $("#close_class_alert").removeAttr("hidden");
        } else if ($(this).val() === "off") {
            $("#open_class_alert").css("top", position["top"] - height - 20);
            $("#open_class_alert").removeAttr("hidden");
        }
        clicked_checkbox_id = $(this).attr("id");
    });

    $(document).on("click", "#accept_close_class_btn", function (event) {
        event.preventDefault();
        div_id = $(this).closest("div .alert_panel").attr("id");
        form_id = $("#" + clicked_checkbox_id).closest("form").attr("id");
        hidden_id = $("#" + form_id + "_hidden").attr("id");
        hidden_value = $("#" + hidden_id).val();
        if (hidden_value === "1") {
            $("#" + hidden_id).val(0);
            $("#" + clicked_checkbox_id).val("off");
            $("#" + clicked_checkbox_id).removeAttr("checked");
        } else {
            $("#" + hidden_id).val(1);
            $("#" + clicked_checkbox_id).val("on");
            $("#" + clicked_checkbox_id).prop("checked", true);
        }
        initiate_submit_form($("#" + clicked_checkbox_id), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            $("#" + div_id).attr("hidden", true);
            show_status_bar("success", ajax_data.success);
            $("td input[type='checkbox']").removeAttr("disabled");
        });
    });

    $(document).on("click", "#cancel_close_class_btn", function (event) {
        event.preventDefault();
        $("#" + clicked_checkbox_id).prop("checked", true);
        $("#close_class_alert").attr("hidden", true);
        $("td input[type='checkbox']").removeAttr("disabled");
    });

    $(document).on("click", ".clickable_row .click_me", function (event) {
        event.preventDefault();
        alert("Nothing happened");
    });

    $(document).on("click", ".edit_class", function (event) {
        event.preventDefault();
        
        var id = $(this).closest("tr").children().last().attr("id");
        var user_type_id = $(this).closest("tr").children().last().attr("user_type_id");
        initiate_submit_get($(this), "find_class.php?class_id=" + id, function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            if (user_type_id === "1") {
                $("#class_title").val(ajax_data.class.title);
                if (ajax_data.class.open === "1") {
                    $("#class_open").attr("checked", true);
                }
                $("#school_id").text(ajax_data.class.school_name);
            }
            $("#class_begin").val(ajax_data.class.start_date);
            $("#class_end").val(ajax_data.class.end_date);
            $("#school_id").val(ajax_data.class.school_id);
            $("#class_description").val(ajax_data.class.description);
            $("#update_class_id").val(ajax_data.class.id);

            $("#select_school").attr("data-plugin", "select2");
            $("#edit_class_a").click();
        });
    });

    $(document).on("change", ".create_select_school", function (event) {
        if ($(this).find("option:selected").val() === "default") {
            $(".create_select_class").css("visibility", "hidden");
        } else {
            event.preventDefault;
            initiate_submit_get($(this), "create_account.php?step=get_classes&school_id=" + $(this).find("option:selected").val(), function () {
                show_status_bar("error", ajax_data.error);
            }, function () {
                $("#select_class_name").html(ajax_data.classes);
                $(".create_select_class").css("visibility", "visible");
            });
        }
    });

    $(document).on("change", ".create_select_usertype", function (event) {
        if ($(this).find("option:selected").val() === "SA") {
            $(".create_select_class").css("visibility", "hidden");
            $(".create_select_school").css("visibility", "hidden");
        } else {
            event.preventDefault;
            $(".create_select_school").css("visibility", "visible");
        }
    });
    //

    // school
    $(document).on("click", ".create_school", function (event) {
        event.preventDefault();
        switch ($(this).attr("step")) {
            case "1":
                $("#create_school_hidden_field_step_1").attr("value", $(this).attr("step"));
                initiate_submit_form($(this), function () {
                    show_status_bar("error", ajax_data.error);
                }, function () {
                    // start step 2 - success
                    $("#step_one").addClass("hidden");
                    $("#step_two").removeClass("hidden");
                    $("#step_one_progress").addClass("progress-bar-success");
                    $("#step_two_progress").switchClass("progress-bar-inactive", "progress-bar");
                    $("#school_subscription_end").datepicker();
                    show_status_bar("success", ajax_data.success);
                });
                break;
            case "2":
                $("#create_school_hidden_field_step_2").val($(this).attr("step"));
                initiate_submit_form($(this), function () {
                    show_status_bar("error", ajax_data.error);
                }, function () {
                    // start step 3 - success
                    $("#step_two").addClass("hidden");
                    $("#step_three").removeClass("hidden");
                    $("#step_two_progress").addClass("progress-bar-success");
                    $("#step_three_progress").switchClass("progress-bar-inactive", "progress-bar");
                    show_status_bar("success", ajax_data.success);
                });
                break;
        }
    });
    //

    // class
    $(document).on("click", ".create_class", function (event) {
        event.preventDefault;
        $("#hidden_description").val($("#class_description").val());
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            setTimeout(function () {
                change_page("create_class")
            }, 300);
            show_status_bar("success", ajax_data.success);
        });
    });

    // global functions
    function preload(arrayOfImages) {
        $(arrayOfImages).each(function () {
            $('<img/>')[0].src = this;
        });
    }

    function initial_page_load() {
        var page_reload = $.cookie("page_reload");
        $.removeCookie("page_reload");

        var pagename = page_reload === "true" ? "front" : $.cookie("current_page") !== undefined ? $.cookie("current_page") : "front";
        var page_step = page_reload === "true" ? "" : $.cookie("current_page_step") !== undefined ? $.cookie("current_page_step") : "";
        var page_args = page_reload === "true" ? "" : $.cookie("current_page_args") !== undefined ? $.cookie("current_page_args") : "";
        change_page(pagename, page_step, page_args);
    }

    function reload_page() {
        var date = new Date();
        date.setTime(date.getTime() + (60 * 1000));
        $.cookie("page_reload", "true", {expires: 10});
        $.removeCookie("current_page");
        $.removeCookie("current_page_step");
        $.removeCookie("current_page_args");
        location.reload();
    }

    $(function () {
        preload([
            'assets/images/loading_page.GIF'
        ]);
    });

    $(document).on("click", ".close_status_bar", function (event) {
        event.preventDefault;
        $(this).closest('div.alert').css("opacity", 0);
        $('#status_container').css("bottom", 0);
        $('#status_container').addClass("hidden");
    });

    function show_status_bar(status_type, message, custom_fade_out) {
        clearTimeout(status_bar_timeout);
        $('#status_container').removeClass("hidden");
        $('div.alert').each(function (e) {
            $(this).attr("style", "display: none; opacity: 0");
        });

        var current_element;
        switch (status_type) {
            default:
                current_element = $('.status_bar.alert-danger');
                break;

            case "success":
                current_element = $('.status_bar.alert-success')
                break;

            case "warning":
                current_element = $('.status_bar.alert-warning')
                break;
        }
        current_element.find("span.status_bar_message").html(message);
        current_element.css("display", "inline-block");
        current_element.fadeTo(300, 1);

        $('#status_container').css("bottom", 0);
        $('#status_container').animate({bottom: 50}, 300);
        var fade_out = custom_fade_out === undefined ? 3000 : custom_fade_out;
        if (fade_out !== 0) {
            status_bar_timeout = setTimeout(function () {
                current_element.fadeTo(300, 0);
                $('#status_container').addClass("hidden");
            }, fade_out);
        }
    }
    //
});

function cursor_wait()
{
    var elements = $(':hover');
    if (elements.length) {
        elements.last().addClass('cursor-wait');
    }
    $('html').off('mouseover.cursorwait').on('mouseover.cursorwait', function (e) {
        $(e.target).addClass('cursor-wait');
    });
}

function remove_cursor_wait() {
    $('html').off('mouseover.cursorwait');
    $('.cursor-wait').removeClass('cursor-wait');
}

