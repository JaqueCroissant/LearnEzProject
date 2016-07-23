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
                        } else if (action === "read") {
                            ajax_data.affected_notifs.forEach(function (o) {
                                $(".notif_count_" + o).removeClass("notif_unread");
                                $(".notif_count_" + o).addClass("notif_read");
                            });
                        }
                    } else {
                        alert(ajax_data.error);
                    }
                }
            }, $(this).attr("args"), $(this).attr("target_form"));
        }
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
            show_status_bar("success", ajax_data.success);
        });
    });


    $(document).on("click", ".create_submit_csv", function (event) {

        var formData = new FormData($('#create_import_form')[0]);
        $.ajax({
            url: 'include/ajax/create_account.php?step=import_users',
            type: 'POST',
            data: formData,
            dataType: "json",
            async: false,
            complete: function (data) {
                ajax_data = $.parseJSON(JSON.stringify(data.responseJSON));
                if(ajax_data.status_value === "true")
                {
                    show_status_bar("success", ajax_data.success);
                }
                else
                {
                    show_status_bar("error", ajax_data.error);
                }
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


    $(document).on("change", ".create_select_school", function (event) {
        if ($(this).find("option:selected").val() === "") {
            $(".create_select_class").css("visibility", "hidden");
            $(".create_select_class").find(".select2-selection__rendered").empty();
        } else if($(".create_select_usertype").find("option:selected").val() === "A"){
            $(".create_select_class").css("visibility", "hidden");
            $(".create_select_class").find(".select2-selection__rendered").empty();
        } else {
            event.preventDefault;
            initiate_submit_get($(this), "create_account.php?step=get_classes&school_id=" + $(this).find("option:selected").val(), function () {
                show_status_bar("error", ajax_data.error);
            }, function () {
                $(".create_select_class").find(".select2-selection__rendered").empty();
                $("#select_class_name").html(ajax_data.classes);
                $(".create_select_class").css("visibility", "visible");
            });
        }
    });

    $(document).on("change", ".import_select_school", function (event) {
        if ($(this).find("option:selected").val() === "") {
            $(".import_select_class").prop("style", "visibility:hidden;height:0px;");
        } else {
            event.preventDefault;
            initiate_submit_get($(this), "create_account.php?step=get_classes&school_id=" + $(this).find("option:selected").val(), function () {
                show_status_bar("error", ajax_data.error);
            }, function () {
                $(".import_select_class").find(".select2-selection__rendered").empty();
                $("#import_class_name").html(ajax_data.classes);
                $(".import_select_class").prop("style", "visibility:visible;height:auto;");
            });
        }
    });

    $(document).on("change", ".create_select_usertype", function (event) {
        if ($(this).find("option:selected").val() === "SA") {
            $(".create_select_class").css("visibility", "hidden");
            $(".create_select_school").css("visibility", "hidden");
        } else if($(this).find("option:selected").val() === "A"){
            $(".create_select_class").css("visibility", "hidden");
            $(".create_select_school").css("visibility", "visible");
        } else {
            event.preventDefault;
            $(".create_select_school").css("visibility", "visible");
        }
    });

    $(document).on("change", ".create_select_usertype_no_school", function (event) {
        if ($(this).find("option:selected").val() === "A") {
            $(".create_select_class").css("visibility", "hidden");
            $(".create_select_school").css("visibility", "hidden");
        } else {
            event.preventDefault;
            $(".create_select_class").css("visibility", "visible");
        }
    });
    //

    // school
    
    //


    // tables
    $(document).on("click", ".clickable_row .click_me", function (event) {
        event.preventDefault();
        alert("Nothing happened");
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
    //
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

