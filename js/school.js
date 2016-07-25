var alert_div_id;

$(document).ready(function () {

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
                    show_status_bar("success", ajax_data.success);
                });
                break;
            case "2":
                $("#create_school_hidden_field_step_2").attr("value", $(this).attr("step"));
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

    $(document).on("click", ".edit_school", function (event) {
        event.preventDefault();
        change_page("edit_school", "", "&school_id=" + $(this).attr("school_id"));
    });
    
    $(document).on("click", ".update_school", function (event) {
        event.preventDefault();

        ajax_data = undefined;
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            show_status_bar("success", ajax_data.success);
        });
    });

    // Close school
    $(document).on("click", ".btn_school_open", function (event) {
        event.preventDefault();

        $("td input[type='checkbox']").attr("disabled", true);
        position = $(this).offset();
        height = $("#close_school_alert").height();
        if ($(this).val() === "on") {
            $("#close_school_alert").css("top", position["top"] - height - 170);
            $("#close_school_alert").removeClass("hidden");
        } else if ($(this).val() === "off") {
            $("#open_school_alert").css("top", position["top"] - height - 170);
            $("#open_school_alert").removeClass("hidden");
        }
        clicked_checkbox_id = $(this).attr("id");
    });
    
        $(document).on("click", ".accept_close_school_btn", function (event) {
        event.preventDefault();
        alert_div_id = $(this).closest("div .alert_panel").attr("id");
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
            $("#" + alert_div_id).addClass("hidden");
            $("td input[type='checkbox']").removeAttr("disabled");
        }, function () {
            $("#" + alert_div_id).addClass("hidden");
            show_status_bar("success", ajax_data.success);
            $("td input[type='checkbox']").removeAttr("disabled");
        });
    });

    $(document).on("click", ".cancel_close_school_btn", function (event) {
        event.preventDefault();
        alert_div_id = $(this).closest("div .alert_panel").attr("id");
        if ($("#" + clicked_checkbox_id).prop("checked") === true) {
            $("#" + clicked_checkbox_id).prop("checked", true);            
        } else {
            $("#" + clicked_checkbox_id).removeAttr("checked");

        }
        $("#" + alert_div_id).addClass("hidden");
        $("td input[type='checkbox']").removeAttr("disabled");
    });
    //
});