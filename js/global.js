var currently_changing_page = false;
var currently_submitting_form = false;
var currently_submitting_get = false;
var content_hidden = false;
var ready_to_change = true;
var status_bar_timeout;

var is_error_page = false;
var page_state;
var current_form;
var current_thumbnail_id;
var current_datatable;
var clicked_checkbox_id;
var clicked_element_id;
var delete_class_id;
var school;
var ajax_data;
var current_lang_id;

//ongoing click events
var alert_box_open = false;

$(document).ready(function () {
    // Load on startup.
    initial_page_load();
    // Load on startup.
    $(".datepickers").datepicker({
        dateFormat: "yy-mm-dd"
    });

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
    $(window).on("resize", function () {
        try {
            $("select[data-plugin='select2']").select2();
        } catch (ex) {
        }
    });

    $(document).on("click", ".change_page_from_overlay", function (event) {
        if (currently_changing_page === false && $(this).attr("clickable") !== "false" && !$(this).attr('disabled')) {
            $(this).attr("clickable", false);
            event.preventDefault();
            var page = $(this).attr("page");
            var step = $(this).attr("step");
            var args = $(this).attr("args");
            change_page_from_overlay(page, step, args, $(this));
        }
    });

    $(document).on("click", ".display_login_overlay", function (event) {
        $(".login_overlay").fadeIn(500);
    });

    $(document).on("click", ".my_tab_header", function (event) {
        event.preventDefault();
        if (ready_to_change) {
            ready_to_change = false;
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
            ready_to_change = true;
        }
    });

    $(document).on("click", ".check_all", function (event) {
        event.preventDefault();
        var form = $(this).attr("target_form");
        var checkboxes = $("#" + form).find(':checkbox').not(":disabled");
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





    // DataTable custom
    $(document).on("length.dt", function (e, settings, len) {
        var api = new $.fn.dataTable.Api(settings);
        table_footer(api);
    });

    $(document).on("init.dt", function (e, settings, json) {
        var api = new $.fn.dataTable.Api(settings);
        table_footer(api);
    });
    //

    $(document).on("click", ".submit_login", function (event) {
        event.preventDefault();

        initiate_submit_form($(this), function () {

            if (ajax_data.user_setup !== undefined) {
                change_page_from_overlay("login");
            } else
            {
                show_status_bar("error", ajax_data.error);
            }
        }, function () {
            $.removeCookie("current_task", {path: '/'});
            reload_page();
        });
    });

    $(document).on("click", ".log_out", function (event) {
        event.preventDefault();
        initiate_submit_get($(this), "login.php?logout=true", function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            $.removeCookie("navigation", {path: '/'});
            $.removeCookie("current_task", {path: '/'});
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
                show_status_bar("error", ajax_data.error);
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
                                $(".notif_count_" + o).removeClass("item_unread");
                            });
                        }
                    } else {
                        show_status_bar("error", ajax_data.error);
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
            show_status_bar("success", ajax_data.success);
        });
    });

    $(document).on("click", ".avatar-hover", function (event) {
        event.preventDefault();

        var avatar_id = $(this).attr("avatar_id");
        if (avatar_id === undefined) {
            return;
        }
        $(".current-avatar").attr("src", "assets/images/profile_images/" + avatar_id);
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




    $(document).on("click", ".contact_submit_info", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            if (ajax_data.reload) {
                setTimeout(function () {
                    change_page("contact");
                }, 500);
            }
            show_status_bar("success", ajax_data.success);
        });
    });

    $(document).on("click", ".create_submit_info", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            if (ajax_data.reload) {
                setTimeout(function () {
                    location.reload();
                }, 500);
            }
            show_status_bar("success", ajax_data.success);
        });
    });

    $(document).on("click", ".create_submit_info_exp", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            $("#accept_box").removeClass("hidden");
            $("#username_text").append(" <b>" + ajax_data.username + "</b>");
            if (ajax_data.reload) {
                setTimeout(function () {
                    location.reload();
                }, 500);
            }
            show_status_bar("success", ajax_data.success);
        });
    });

    $(document).on("click", ".create_submit_changed_password", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            $(".login_overlay").fadeIn(500);
            show_status_bar("success", ajax_data.success);
        });
    });

    $(document).on("click", ".account_assign_password", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            if (ajax_data.reload) {
                setTimeout(function () {
                    location.reload();
                }, 500);
            }
            show_status_bar("success", ajax_data.success);

            var url = ajax_data.host + "/include/pages/printable_passwords.php";

            if (!/^(f|ht)tps?:\/\//i.test(url))
            {
                url = "http://" + url;
            }

            window.location = url;
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
                if (ajax_data.status_value)
                {
                    show_status_bar("success", ajax_data.success);
                }
                else
                {
                    if(ajax_data.has_add_info)
                    {
                        show_status_bar("error", ajax_data.error + " " + ajax_data.add_info);
                    }
                    else
                    {
                        show_status_bar("error", ajax_data.error);
                    }
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

            show_status_bar("success", ajax_data.success);
        });
    });

    $(document).on("click", ".update_acc_generate_pass", function (event) {
        event.preventDefault();

        initiate_submit_get($(this), "edit_account.php?step=generate_password", function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            $("#edit_password").attr("value", ajax_data.password);
        });
    });

    $(document).on("click", ".update_account_submit", function (event) {
        event.preventDefault();
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            if (ajax_data.reload) {
                setTimeout(function () {
                    location.reload();
                }, 500);
            }
            show_status_bar("success", ajax_data.success);
        });
    });


    $(document).on("click", ".close_terms", function (event) {
        event.preventDefault();
        $.cookie("cookie_terms", true, {expires: 365});
        $(".cookie_overlay").fadeOut(250, function () {
            $("cookie_overlay").css("display: none !important");
        });

    });


    $(document).on("change", ".create_select_school", function (event) {
        if ($(this).find("option:selected").val() === "") {
            $(".create_select_class").css("visibility", "hidden");
            $(".create_select_class").find(".select2-selection__rendered").empty();
        } else if ($(".create_select_usertype").find("option:selected").val() === "A") {
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
        } else if ($(this).find("option:selected").val() === "A") {
            $(".create_select_class").css("visibility", "hidden");
            $(".create_select_school").css("visibility", "visible");
        } else {
            event.preventDefault;

            $(".create_select_school").css("visibility", "visible");
            if ($(".create_select_school").find("option:selected").val() !== "")
            {
                $(".create_select_class").css("visibility", "visible");
            }
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


    // tables
    $(document).on("click", ".clickable_row .click_select_me", function (event) {
        event.preventDefault();
        var current_checkbox = $(this).parent("tr").find("input[type=checkbox]");
        current_checkbox.prop("checked", !current_checkbox.prop("checked"));
    });

    // global functions
    function preload(arrayOfImages) {
        $(arrayOfImages).each(function () {
            $('<img/>')[0].src = this;
        });
    }

    function initial_page_load() {
        var params = getSearchParameters();

        if (params.page !== undefined) {
            var args = "";
            $.each(params, function (key, value) {
                if (key !== "page" && key !== "step") {
                    args += "&" + key + "=" + value;
                }
            });
            change_page(params.page, params.step, args);
            return;
        }

        var page_reload = $.cookie("page_reload");
        $.removeCookie("page_reload");

        if ($.cookie("navigation") !== undefined) {
            var navigation = JSON.parse($.cookie("navigation"));
            var last_element = navigation[Object.keys(navigation)[Object.keys(navigation).length - 1]];
        }

        if (page_reload !== "true") {
            if (last_element !== undefined) {
                change_page(last_element.page, last_element.step, last_element.args);
                return;
            }
            change_page("account_overview", "", "");
            return;
        }
        change_page("account_overview", "", "");
    }

    $(document).on("click", ".go_back", function () {
        if ($.cookie("navigation") !== undefined) {
            var navigation = $.map(JSON.parse($.cookie("navigation")), function (value, index) {
                return [value];
            });

            if ((navigation.length < 2 && !is_error_page) || (navigation.length < 1 && is_error_page)) {
                return;
            }

            var last_page = navigation.pop();
            if (is_error_page !== true) {
                last_page = navigation.pop();
            }

            $.cookie("navigation", JSON.stringify(navigation), {expires: 10, path: '/'});
            change_page(last_page.page, last_page.step, last_page.args);
        }
    });

    function reload_page() {
        var date = new Date();
        date.setTime(date.getTime() + (60 * 1000));
        $.cookie("page_reload", "true", {expires: 10});
        //$.removeCookie("navigation", {path: '/'});
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

var audioElement;

function reload_page_content(pagename) {
    if ($.cookie("navigation") !== undefined) {
        var navigation = $.map(JSON.parse($.cookie("navigation")), function (value, index) {
            return [value];
        });

        if (navigation.length < 1) {
            return;
        }

        var last_page = navigation.pop();
        if (pagename === undefined) {
            return;
        }

        if (pagename === last_page.page) {
            change_page(last_page.page, last_page.step, last_page.args, undefined, false);
        }
    }
}

function table_footer(api) {
    if (api.page.len() > 15) {
        $("tfoot").removeClass("hidden");
    } else if (!$("tfoot").hasClass("hidden")) {
        $("tfoot").addClass("hidden");
    }
}

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

//Show achievements
function show_achievements() {
    if ($.cookie("achievements") !== undefined) {
        var left = parseInt($("#aside-inner-scroll").width()) + parseInt($(".wrap").css("padding-left"));
        var element = $("#achievement_container");
        element.removeClass("hidden");
        element.css("position", "fixed");
        element.css("left", left + "px");
        element.css("bottom", 0);
        display_achievement(element);
    }
}

function display_achievement(element) {
    if ($.cookie("achievements") !== undefined) {
        var list = $.parseJSON($.cookie("achievements"));
        if (list.length === 0) {
            element.addClass("hidden");
            $.removeCookie("achievements", {path: "/"});
            return true;
        }
        var value = list.shift();
        console.log(value);
        var title = value.title === undefined ? "" : value.title;
        var count = value.count === undefined || value.count === "0" ? "" : value.count + " ";
        var text = value.text === undefined ? "" : value.text;
        element.css("display", "inline-block");
        element.removeClass("hidden");
        $("#achievement_title").html(title);
        $("#achievement_txt").html(text);
        $("#achievement_img").attr("src", "assets/images/achievement/" + value.img_path + "-200.png");
        element.addClass("achievement_animation");
        element_timeout = setTimeout(function () {
            element.fadeTo(0, 2000, function () {
                element.removeClass("achievement_animation");
                element.css("display", "none");
                setTimeout(function () {
                    element.removeClass("achievement_animation_reverse");
                    if (list.length === 0) {
                        element.addClass("hidden");
                        $.removeCookie("achievements", {path: "/"});
                        return;
                    } else {
                        $.cookie("achievements", JSON.stringify(list), {path: "/"});
                        display_achievement(element);
                    }
                }, 2000);
            });
        }, 5000);
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

function getSearchParameters() {
    var prmstr = window.location.search.substr(1);
    return prmstr != null && prmstr != "" ? transformToAssocArray(prmstr) : {};
}
function transformToAssocArray(prmstr) {
    var params = {};
    var prmarr = prmstr.split("&");
    for (var i = 0; i < prmarr.length; i++) {
        var tmparr = prmarr[i].split("=");
        params[tmparr[0]] = tmparr[1];
    }
    return params;
}

function clearUrl() {
    var index = 0;
    var count = 1;
    for (var i = 0, len = window.location.href.length; i < len; i++) {
        if (window.location.href[i] == '/' && count < 3) {
            count++;
            index = i;
        }
    }
    window.history.pushState("1", "Title", "/" + window.location.href.substring(index, window.location.href.lastIndexOf('/') + 1).split("?")[0]);
}

