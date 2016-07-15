var currently_changing_page = false;
var currently_submitting_form = false;
var currently_submitting_get = false;
var content_hidden = false;

var clicked_checkbox_id;
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
    //

    // login / logout
    $(document).on("click", ".submit_login", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            alert(ajax_data.error);
        }, function () {
            reload_page();
        });
    });


    $(document).on("click", ".log_out", function (event) {
        event.preventDefault();
        initiate_submit_get($(this), "login.php?logout=true", function () {
            alert(ajax_data.error);
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
                alert(ajax_data.error);
            }, function () {
                change_page("mail", current_page);
            });
        } else {
            if ($("#" + $(this).attr("target_form") + " input:checkbox:checked").length > 0) {
                initiate_custom_submit_form($(this), function () {
                    alert(ajax_data.error);
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
            alert(ajax_data.error);
        }, function () {
        });
    });

    $(document).on("click", ".submit_reply_mail", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            alert(ajax_data.error);
        }, function () {
            $(".reply_form").fadeOut(500);
        });
   });
   //
   
   // rights
   $(document).on("click", ".submit_change_rights", function(event){
        event.preventDefault();
        initiate_submit_form($(this), function() {
            alert(ajax_data.error);
        }, function() {
        });
   });
   
   // edit user info
    $(document).on("click", ".submit_edit_user_info", function(event){
        event.preventDefault();
        initiate_submit_form($(this), function () {
            alert(ajax_data.error);
        }, function () {
            $(".username").html(ajax_data.full_name);
            $(".current-avatar-image").attr("src", "assets/images/profile_images/" + ajax_data.avatar_id + ".png");
        });
    });

    $(document).on("click", ".avatar-hover", function(event){
            event.preventDefault();

            var avatar_id = $(this).attr("avatar_id");
            if(avatar_id === undefined) {
                return;
            }
            $(".current-avatar").attr("src", "assets/images/profile_images/" + avatar_id + ".png");
            $(".input_avatar_id").val(avatar_id);

    });

    $(document).on("input", "input.input_change", function(event){
            event.preventDefault();
            $(".user_full_name").html($(".input_firstname").val() + " " + $(".input_surname").val());
    });

    $(document).on("click", ".settings_submit_password", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            alert(ajax_data.error);
        }, function () {
        });
    });

    $(document).on("click", ".create_submit_info", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            alert(ajax_data.error);
        }, function () {
        });
    });


   $(document).on("click", ".create_submit_csv", function(event){
       var formData = new FormData($(this)[0]);

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
        initiate_submit_form($(this), function() {
            alert(ajax_data.error);
        }, function() {
        });
   });

   $(document).on("click", ".reset_pass_submit_email2", function(event){
        event.preventDefault;
        initiate_submit_form($(this), function () {
            alert(ajax_data.error);
        }, function () {
            location.reload();
        });
    });

    $(document).on("click", ".btn_class_open", function (event) {
        event.preventDefault;
        
        if ($(this).val() === "on") {
            alert("on");
        } else if ($(this).val() === "off") {
            alert("off");
        }
        $("td input[type='checkbox']").attr("disabled", true);
        position = $(this).offset();
        height = $("#close_class_alert").height();
        $("#close_class_alert").css("top", position["top"] - height - 20);
        $("#close_class_alert").removeAttr("hidden");
        clicked_checkbox_id = $(this).attr("id");
    });

    $(document).on("click", "#accept_close_class_btn", function (event) {
        form_id = $("#" + clicked_checkbox_id).closest("form").attr("id");
        hidden_id = $("#" + form_id + "_hidden").attr("id");
        hidden_value = $("#" + hidden_id).val();
        if (hidden_value === "1") {
            $("#" + hidden_id).val(0);
            $("#" + clicked_checkbox_id).val("off");
        } else {
            $("#" + hidden_id).val(1);
            $("#" + clicked_checkbox_id).val("on");
        }
        initiate_submit_form($("#" + clicked_checkbox_id), function () {
            alert(ajax_data.error);
        }, function () {
            $("#close_class_alert").attr("hidden", true);
            $("td input[type='checkbox']").removeAttr("disabled");
        });
    });

    $(document).on("click", "#cancel_close_class_btn", function (event) {
        $("#" + clicked_checkbox_id).prop("checked", true);
        $("#close_class_alert").attr("hidden", true);
        $("td input[type='checkbox']").removeAttr("disabled");
    });

    $(document).on("click", ".clickable_row .click_me", function (event) {
        alert("fuck yeah - Metoden skal hive dataen ud af clicked row og levere til rediger klasse siden.");
    });

    $(document).on("change", ".create_select_school", function (event) {
        if ($(this).find("option:selected").val() === "default") {
            $(".create_select_class").css("visibility", "hidden");
        } else {
            event.preventDefault;
            initiate_submit_get($(this), "create_account.php?step=get_classes&school_id=" + $(this).find("option:selected").val(), function () {
                alert(ajax_data.error);
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
                    alert(ajax_data.error); // fail function
                }, function () {
                    // start step 2 - success
                    $("#step_one").addClass("hidden");
                    $("#step_two").removeClass("hidden");
                    $("#step_one_progress").addClass("progress-bar-success");
                    $("#step_two_progress").switchClass("progress-bar-inactive", "progress-bar");
                    $("#school_subscription_end").datepicker();
                });
                break;
            case "2":
                $("#create_school_hidden_field_step_2").val($(this).attr("step"));
                initiate_submit_form($(this), function () {
                    alert(ajax_data.error); // fail function
                }, function () {
                    // start step 3 - success
                    $("#step_two").addClass("hidden");
                    $("#step_three").removeClass("hidden");
                    $("#step_two_progress").addClass("progress-bar-success");
                    $("#step_three_progress").switchClass("progress-bar-inactive", "progress-bar");
                });
                break;
        }
    });
    //

    // class
    $(document).on("click", ".create_class", function (event) {
        event.preventDefault;
        initiate_submit_form($(this), function () {
            alert(ajax_data.error);
        }, function () {
            location.reload();
            alert("Class created - replace alert with snackbar");
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

