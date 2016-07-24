var delete_class_id;

$(document).ready(function () {
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
        var count = $("#class_open:checked").length;
        if (count === 1) {
            $("#class_open_hidden").val(1);
        } else if (count === 0) {
            $("#class_open_hidden").val(0);
        }

        $("#hidden_description").val($("#class_description").val());
        ajax_data = undefined;
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            change_page("edit_class");
            show_status_bar("success", ajax_data.success);
        });
    });

    $(document).on("click", ".delete_class", function (event) {
        event.preventDefault();

        position = $(this).offset();
        height = $("#close_class_alert").height();
        $("#delete_class_alert").css("top", position["top"] - height - 20);
        $("#delete_class_alert").removeAttr("hidden");
        delete_class_id = $(this).attr("school_id");
    });

    $(document).on("click", "#accept_delete_class_btn", function (event) {
        event.preventDefault();

        div_id = $(this).closest("div .alert_panel").attr("id");
        var id = $("#" + delete_class_id).closest("tr").children().last().attr("id");
        console.log(id);
        initiate_submit_get($(this), "edit_class.php?class_id=" + id + "&state=delete_class", function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            $("#" + div_id).attr("hidden", true);
            change_page("find_class");
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
            $("#" + clicked_checkbox_id).val("on");
            $("#" + clicked_checkbox_id).prop("checked", true);
            $("#" + div_id).attr("hidden", true);
            $("td input[type='checkbox']").removeAttr("disabled");
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



    $(document).on("click", ".edit_class", function (event) {
        event.preventDefault();

        var id = $(this).closest("tr").children().last().attr("id");
        change_page("edit_class", "" ,"&class_id=" + id);
    });
});