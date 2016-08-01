$(document).on("click", ".upload_thumbnail", function (event) {
    var form = $(this).closest("form");
    event.preventDefault();
    var formData = new FormData(form[0]);
    $.ajax({
        url: 'include/ajax/course.php?step=upload_thumbnail',
        type: 'POST',
        data: formData,
        dataType: "json",
        async: false,
        complete: function (data) {
            ajax_data = $.parseJSON(JSON.stringify(data.responseJSON));
            if (ajax_data.status_value) {
                show_status_bar("success", ajax_data.success);
                update_thumbnails();
            } else {
                show_status_bar("error", ajax_data.error);
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
});

$(document).on({
    mouseenter: function () {
        $(this).find(".delete_thumbnail").removeClass("hidden");
        $(this).find(".set_default_thumbnail").removeClass("hidden");
        $(this).css({ opacity: 1 });
    },
    mouseleave: function () {
       $(this).find(".delete_thumbnail").addClass("hidden");
       var set_default_thumbnail = $(this).find(".set_default_thumbnail");
       
       if(set_default_thumbnail.attr("default_thumbnail") !== "1") {
           set_default_thumbnail.addClass("hidden");
       }
       
       if($(this).attr("thumbnail_id") !== current_thumbnail_id && current_thumbnail_id !== undefined) {
           $(this).css({ opacity: 0.5 });
       }
    }
}, ".thumbnail_element");

$(document).on("click", ".thumbnail_element", function(event) {
    if($(this).attr("thumbnail_id") === current_thumbnail_id) {
        $(".active_thumbnail").addClass('hidden');
        $(".thumbnail_element").css({ opacity: 1 });
        current_thumbnail_id = undefined;
        $(".thumbnail_picked").val(0);
        return;
    }
    
    current_thumbnail_id = $(this).attr("thumbnail_id");
    $(".thumbnail_picked").val(current_thumbnail_id);
    var current_thumbnail = $(this).find(".active_thumbnail");
    current_thumbnail.removeClass("hidden");
    $(".active_thumbnail").not(current_thumbnail).addClass('hidden');
    $(".thumbnail_element").not($(this)).css({ opacity: 0.5 });
});


$(document).on("click", ".delete_thumbnail", function (event) {
    event.stopPropagation();
    var form = $(this).closest("form");
    var thumbnail_id = $(this).attr("thumbnail_id");
    event.preventDefault();
    initiate_submit_get($(this), "course.php?delete_thumbnail=1&thumbnail_id="+thumbnail_id, function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        update_thumbnails();
        if($(".thumbnail_picked").val() === thumbnail_id) {
            $(".active_thumbnail").addClass('hidden');
            $(".thumbnail_element").css({ opacity: 1 });
            current_thumbnail_id = undefined;
        }
    });
});

$(document).on("click", ".set_default_thumbnail", function (event) {
    event.stopPropagation();
    var form = $(this).closest("form");
    var thumbnail_id = $(this).attr("thumbnail_id");
    event.preventDefault();
    initiate_submit_get($(this), "course.php?set_default_thumbnail=1&thumbnail_id="+thumbnail_id, function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        update_thumbnails();
    });
});

function update_thumbnails() {
    var url = "course.php?get_thumbnails" + (current_thumbnail_id !== undefined ? "&selected_thumbnail=" + current_thumbnail_id : "");
    initiate_submit_get($(this), url, function () {}, function () {
        if(ajax_data.thumbnails !== undefined) {
            $(".thumbnail-placeholder").html(ajax_data.thumbnails);
        }
    });
}

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

$(document).on("click", ".submit_update_course", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
    });
});

$(document).on("change", ".add_courses", function (event) {
    var form = $(this).closest("form");
    var os_id = $(this).find("option:selected").val();
    event.preventDefault();
    initiate_submit_get($(this), "course.php?get_courses=1&os_id="+os_id, function () {
        show_status_bar("error", ajax_data.error);
        form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
        form.find("#sort_order").empty();
    }, function () {
        if(ajax_data.courses !== "") {
            form.find(".sort_order").attr("style", "height:auto;opacity:1;");
            form.find("#sort_order").html(ajax_data.courses);
            var search = form.find("#sort_order");
            search.select2();
        } else {
            form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
            form.find("#sort_order").empty();
        }
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






