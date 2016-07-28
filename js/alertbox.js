// Open/close
    $(document).on("change", ".btn_alertbox", function (event) {
        event.preventDefault();
        $("td input[type='checkbox']").attr("disabled", true);
        var text = !$(this).is(":checked") ? $("#open_text").text() : $("#close_text").text();
        $("#alertbox").find(".panel-body").text(text);
        $("#alertbox").removeClass("hidden");
        $("#alertbox").css("top", $(this).offset()["top"] - ($("#alertbox").height()));
        clicked_checkbox_id = $(this).attr("element_id");
    });
    
    $(document).on("click", ".accept_alertbox_btn", function (event) {
        event.preventDefault();
        var form = $("#alert_form_" + clicked_checkbox_id);
        initiate_submit_form(form, function () {
            show_status_bar("error", ajax_data.error);
            close_alert_box(true);
        }, function () {
            show_status_bar("success", ajax_data.success);
            close_alert_box(false);
        });
    });

    $(document).on("click", ".cancel_alertbox_btn", function (event) {
        event.preventDefault();
        close_alert_box(true);
    });
    
    function close_alert_box(reverse) {
        if(clicked_checkbox_id === undefined) {
            return;
        }
        
        if(reverse === true) {
           var current_checkbox = $("input[element_id=" + clicked_checkbox_id + "]");
            current_checkbox.prop("checked", !current_checkbox.prop("checked")); 
        }
        
        $("#alertbox").addClass("hidden");
        $("td input[type='checkbox']").removeAttr("disabled");
    }


//On click (delete etc)

    $(document).on("click", ".btn_click_alertbox", function (event) {

        event.preventDefault();
        $("#click_alertbox").removeClass("hidden");
        $("#click_alertbox").css("top", $(this).offset()["top"] - ($("#click_alertbox").height()));
        clicked_element_id = $(this).attr("element_id");

    });

    $(document).on("click", ".accept_click_alertbox_btn", function (event) {
        event.preventDefault();
        var form = $("#click_alert_form_" + clicked_element_id);
        var dataTable = $("#default-datatable").DataTable();
        initiate_submit_form(form, function () {
            show_status_bar("error", ajax_data.error);
            close_alert_box(true);
        }, function () {
            var current_element_tr = ".account_tr_id_" + clicked_element_id;
            dataTable.row(current_element_tr).remove();
            $(current_element_tr).remove();_
            show_status_bar("success", ajax_data.success);
            close_click_alert_box();
        });
    });

    $(document).on("click", ".cancel_click_alertbox_btn", function (event) {
        event.preventDefault();
        close_click_alert_box();
    });

    function close_click_alert_box() {
        if(clicked_element_id === undefined) {
            return;
        }
        $("#click_alertbox").addClass("hidden");
    }