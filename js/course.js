$(document).on("click", ".add_course_translation", function (event) {
    var value = $('#language').find(":selected").val();
    var name = $('#language').find(":selected").text();
    var title = $('.title_text').text();
    var description = $('.description_text').text();
    var translation = $('.translation_text').text();
    
    if(value !== undefined && name !== undefined && !$(".translation_"+value)[0]) {
        var block = $('<div class="translation_'+value+' translation_element"><input type="hidden" name="language_id[]" value="'+value+'"/><div class="panel-group accordion translation_' + value + '" id="accordion" role="tablist" aria-multiselectable="false"><div class="panel panel-default"><div class="panel-heading" role="tab" id="heading-'+ value + '"><a class="accordion-toggle collapsed" style="padding: 10px 0px 5px 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-'+ value + '" aria-expanded="false" aria-controls="collapse-'+ value + '"><label for="textarea'+ value + '" style="cursor:pointer">'+ name + ' ' + translation + '</label><i class="fa acc-switch"></i><i class="zmdi zmdi-hc-lg zmdi-delete pull-right remove_translation" translation_id="'+ value + '" style="margin-top:1px;cursor:pointer;"></i></a></div><hr class="m-0 m-b-md" style="border-color: #ddd;margin: 0px 0px !important;"><div id="collapse-'+ value + '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-'+ value + '" aria-expanded="false" style="height: 0px;"><div class="panel-body" style="padding: 5px 10px 0px 10px !important;"><label for="title" style="margin-bottom:0px !important;">'+ title + '</label><input type="text" id="title" name="title[]" placeholder="" class="form-control"><label for="description" style="margin: 10px 0px 0px 0px !important;">'+ description + '</label><input type="text" id="description" name="description[]" placeholder="" class="form-control"></div></div></div></div></div>').hide().fadeIn(300);
        if($('.no_translations_text').is(":visible")) {
            $('.no_translations_text').fadeOut('300', function() {
                $(".translations").append(block); 
            });
        } else {
            $(".translations").append(block); 
        }
    }
});


$(document).on("click", ".remove_translation", function (event) {
    var value = $(this).attr("translation_id");
    if(value !== undefined) {
        $('.translation_' + value).remove();
        if(!$(".translation_element")[0]) {
            $('.no_translations_text').fadeIn('300');
        }
    }
});

$(document).on("click", ".submit_create_course", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        change_page("handle_course", "create_course");
    });
});