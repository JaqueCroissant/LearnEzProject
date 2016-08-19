$(document).on("input", ".certificate_input", function(event){
    if ($(this).val().length === 4) {
        $(".certificate_input[name='" + (parseInt($(this).attr("name")) + 1) + "']").focus();
    }
    else if ($(this).val().length > 4){
        var temp = $(this).val().match(new RegExp("-", "g"));
        if ($(this).val().length === 24 && temp !== null && temp.length === 4) {
            $(this).val($(this).val().replace(new RegExp("-", "g"), ""));
        }
        $(".certificate_input[name='" + (parseInt($(this).attr("name")) + 1) + "']").val($(this).val().substring(4));
        $(this).val($(this).val().substring(0, 4));
        $(".certificate_input[name='" + (parseInt($(this).attr("name")) + 1) + "']").focus();
        $(".certificate_input[name='" + (parseInt($(this).attr("name")) + 1) + "']").trigger("input");
    }
});

$(document).on("keydown", ".certificate_input", function(event){
    if (event.which === 8 && parseInt($(this).val().length) === 0 && parseInt($(this).attr("name")) !== 1) {
        $(".certificate_input[name='" + (parseInt($(this).attr("name")) - 1) + "']").focus();
    }
});

$(document).on("click", ".certificate_submit", function(){
    initiate_submit_form($(this), function(){
        show_status_bar("error", ajax_data.error);
    }, function(){
        if (ajax_data.status_value === true) {
            
        }
        else {
            show_status_bar("error", ajax_data.error);
        }
    });
});

$(document).on("click", ".download_single_certificate", function(){
    return;
    initiate_submit_get($(this), "download_pdf.php?download_single=" + $(this).attr("element_id"), function(){
        show_status_bar("error", ajax_data.error);
    }, function(){
        show_status_bar("success", ajax_data.success);
    });
});

