// Close school
    $(document).on("click", ".btn_account_open", function (event) {
        event.preventDefault();

        $("td input[type='checkbox']").attr("disabled", true);
        position = $(this).offset();
        height = $("#close_account_alert").height();
        if ($(this).val() === "on") {
            $("#close_account_alert").css("top", position["top"] - height - 170);
            $("#close_account_alert").removeClass("hidden");
        } else if ($(this).val() === "off") {
            $("#open_account_alert").css("top", position["top"] - height - 170);
            $("#open_account_alert").removeClass("hidden");
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