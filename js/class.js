$(document).ready(function (){
// class
    $(document).on("click", ".create_class", function (event) {
        event.preventDefault;
        $("#hidden_description").val($("#class_description").val());
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            change_page("create_class");
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
});