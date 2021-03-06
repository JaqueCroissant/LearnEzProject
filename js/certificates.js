var last_downloaded_time;

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
    $(".certificate_item").hide();
    
    initiate_submit_form($(this), function(){
        show_status_bar("error", ajax_data.error);
    }, function(){
        if (ajax_data.status_value === true) {
            $(".certificate_color").css("border-right-color", ajax_data.course_color);
            $(".certificate_image").attr("src", "assets/images/thumbnails/" + ajax_data.course_image);
            $(".certificate_date").html(ajax_data.complete_date);
            $(".certificate_doneby").html(ajax_data.done_by + "<br/>" + ajax_data.user_firstname + ' ' + ajax_data.user_surname);
            $(".certificate_title").html(ajax_data.course_title);
            $(".certificate_description").html(ajax_data.course_description);
            $(".certificate_item").show();
        }
        else {
            show_status_bar("error", ajax_data.error);
        }
    });
});

$(document).on("click", ".download_single_certificate", function(){
    if($.now() - last_downloaded_time < 5000) {
        show_status_bar("error", $(".wait_translation").text());
        return;
    }
    $(".certificate_iframe").remove();
    var element_id = $(this).closest(".mail-item").attr("element_id");
    initiate_submit_get($(this), "download_pdf.php?step=download_single&element_id=" + element_id, function(){
        show_status_bar("error", ajax_data.error);
    }, function(){
        last_downloaded_time = $.now();
        show_status_bar("success", ajax_data.success);
        download(ajax_data.file_name);
    });
});
    
$(document).on("click", ".certificate_reset", function(){
    $(".certificate_input").val("");
});

$(document).on("click", ".download_checked_certificates", function() {
    if($.now() - last_downloaded_time < 5000) {
        show_status_bar("error", $(".wait_translation").text());
        return;
    }
    $(".certificate_iframe").remove();
    initiate_submit_form($(this), function(){ 
        show_status_bar("error", ajax_data.error);
    }, function(){
        if(ajax_data.file_names !== undefined) {
            last_downloaded_time = $.now();
            for (var i = 0; i < ajax_data.file_names.length; i++) {
                download(ajax_data.file_names[i]);
            }
            show_status_bar("success", ajax_data.success);
        }
    });
});

function download(file) {
    $("#content_container").append('<iframe class="certificate_iframe" style="display:none;" src="include/pages/download_pdf.php?file=' + file + '"></iframe>');
}

