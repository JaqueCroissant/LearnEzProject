$(document).on("change", ".check_course_elements", function (event) {
    event.preventDefault();
    var current_checkbox = $(this);
    var checkbox_id = $(this).attr("checkbox_id");
    $("#collapse-" + checkbox_id + " input").each(function() {
        if (current_checkbox.is(":checked")) {
            $(this).prop('checked', true);
        } else {
            $(this).prop('checked', false);
        }
    });
});

$(document).on("click", ".submit_create_homework", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        change_page("create_homework");
    });
});