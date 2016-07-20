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
        
        var id = $(this).closest("tr").children().last().attr("id");
        initiate_submit_get($(this), "find_school.php?school_id=" + id, function () {
            show_status_bar("error", ajax_data.error);
        }, function () { 
            
        });
    });

});