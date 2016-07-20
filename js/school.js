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
    
    $(document).on("click", ".edit_school", function (event) {
        event.preventDefault();
        
        var id = $(this).closest("tr").children().last().text();
        initiate_submit_get($(this), "find_school.php?school_id=" + id, function () {
            show_status_bar("error", ajax_data.error);
        }, function () { 
            $("#edit_school_name").val(ajax_data.school.name);
            $("#edit_school_address").val(ajax_data.school.address);
            $("#edit_school_zip_code").val(ajax_data.school.zip_code);
            $("#edit_school_city").val(ajax_data.school.city);
            $("#edit_school_phone").val(ajax_data.school.phone);
            $("#edit_school_email").val(ajax_data.school.email);
            $("#edit_school_max_students").val(ajax_data.school.max_students);
            $("#edit_school_subscription_start").val(ajax_data.school.subscription_start);
            $("#edit_school_subscription_end").val(ajax_data.school.subscription_end);
            $("#update_school_id").val(ajax_data.school.id);
            
            $("#edit_school_type_id option").removeAttr("selected");
            $("#school_type_id_" + ajax_data.school.school_type_id).attr("selected", "selected");
            $("#edit_school_type_id").trigger("change");
            
            $("#edit_school_a").click();
        });
    });
    
    $(document).on("click", ".update_school", function (event) {
        event.preventDefault();
        
        ajax_data = undefined;
        initiate_submit_form($(this), function () {
            show_status_bar("error", ajax_data.error);
        }, function () {
            change_page("find_school");
            show_status_bar("success", ajax_data.success);
        });
    });

    $(document).on("click", ".delete_school", function (event) {
        event.preventDefault();
    });
});