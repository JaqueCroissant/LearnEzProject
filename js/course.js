$(document).on("click", ".add_translation", function (event) {
    var form = $(this).closest("form");
    var value = form.find('#language').find(":selected").val();
    var name = form.find('#language').find(":selected").text();
    var title = form.find('.title_text').text();
    var description = form.find('.description_text').text();
    var translation = form.find('.translation_text').text();
    var type = form.find('.translation_type').text();
    
    if(value !== undefined && name !== undefined && !form.find(".translation_"+value)[0]) {
        var block = $('<div class="translation_'+value+' translation_element"><div class="user-card m-b-sm student_20" style="padding: 8px !important;background:#f0f0f1;"><div class="media"><div class="media-body"><input type="hidden" name="language_id[]" value="'+value+'"/><div class="accordion translation_'+ type + '' + value + '" id="accordion" role="tablist" aria-multiselectable="false"><div class=""><div class="panel-heading" role="tab" id="heading-'+ type + ''+ value + '"><a class="accordion-toggle collapsed" style="padding: 5px 0px 0px 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-'+ type + ''+ value + '" aria-expanded="false" aria-controls="collapse-'+ type + ''+ value + '"><label for="textarea'+ value + '" style="cursor:pointer">'+ name + ' ' + translation + '</label><i class="fa acc-switch"></i><i class="zmdi zmdi-hc-lg zmdi-delete pull-right remove_translation" translation_id="'+ value + '" style="margin-top:1px;cursor:pointer;"></i></a></div><div id="collapse-'+ type + ''+ value + '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-'+ type + ''+ value + '" aria-expanded="false" style="height: 0px;"><div class="panel-body" style="padding: 5px 10px 10px 10px !important;"><label for="title" style="margin-bottom:0px !important;">'+ title + '</label><input type="text" id="title" name="title[]" placeholder="" class="form-control"><label for="description" style="margin: 10px 0px 0px 0px !important;">'+ description + '</label><input type="text" id="description" name="description[]" placeholder="" class="form-control"></div></div></div></div></div></div></div></div>').hide().fadeIn(300);
        if(form.find('.no_translations_text').is(":visible")) {
            form.find('.no_translations_text').fadeOut('300', function() {
                form.find(".translations").append(block); 
            });
        } else {
            form.find(".translations").append(block); 
        }
    }
});


$(document).on("click", ".remove_translation", function (event) {
    var form = $(this).closest("form");
    var value = $(this).attr("translation_id");
    if(value !== undefined) {
        form.find('.translation_' + value).remove();
        if(!form.find(".translation_element")[0]) {
            form.find('.no_translations_text').fadeIn('300');
        }
    }
});

$(document).on("click", ".submit_create_course", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        change_page("course_administrate", "create_course");
    });
});

$(document).on("change", ".add_lectures", function (event) {
    var form = $(this).closest("form");
    var course_id = $(this).find("option:selected").val();
    event.preventDefault();
    initiate_submit_get($(this), "course.php?get_lectures=1&course_id="+course_id, function () {
        show_status_bar("error", ajax_data.error);
        form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
        form.find("#sort_order").empty();
    }, function () {
        if(ajax_data.lectures !== "") {
            form.find(".sort_order").attr("style", "height:auto;opacity:1;");
            form.find("#sort_order").html(ajax_data.lectures);
            var search = form.find("#sort_order");
            search.select2();
        } else {
            form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
            form.find("#sort_order").empty();
        }
    });
});

$(document).on("change", ".add_tests", function (event) {
    var form = $(this).closest("form");
    var course_id = $(this).find("option:selected").val();
    event.preventDefault();
    initiate_submit_get($(this), "course.php?get_tests=1&course_id="+course_id, function () {
        show_status_bar("error", ajax_data.error);
        form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
        form.find("#sort_order").empty();
    }, function () {
        if(ajax_data.tests !== "") {
            form.find(".sort_order").attr("style", "height:auto;opacity:1;");
            form.find("#sort_order").html(ajax_data.tests);
            var search = form.find("#sort_order");
            search.select2();
        } else {
            form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
            form.find("#sort_order").empty();
        }
    });
});






