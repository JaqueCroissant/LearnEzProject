
$(document).on("click", ".submit_edit_user_info", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        $(".username").html(ajax_data.full_name);
        $(".current-avatar-image").attr("src", "assets/images/profile_images/" + ajax_data.avatar_id + ".png");
    });
});

$(document).on("click", ".block_student", function (event) {
    var value = $('#student').find(":selected").val();
    var name = $('#student').find(":selected").text();
    if(value !== undefined && name !== undefined && !$(".student_"+value)[0]) {
        var block = $('<div class="div_student_' + value + ' blocked_student" style="display:none;"><input type="hidden" name="blocked_student[]" value="' + value + '" /><div class="user-card m-b-sm student_' + value + '" style="padding: 8px !important;background:#f0f0f1;"><div class="media"><div class="media-body">' + name + '<i class="zmdi zmdi-hc-lg zmdi-close pull-right remove_blocked_student" student_id="' + value + '" style="margin-top:4px;cursor: pointer;"></i></div></div></div></div>').hide().fadeIn(300);
       
        if($('.no_students_text').is(":visible")) {
            $('.no_students_text').fadeOut('300', function() {
                $(".blocked_students").append(block); 
            });
        } else {
            $(".blocked_students").append(block); 
        }
        
    }
});

$(document).on("click", ".remove_blocked_student", function (event) {
    var value = $(this).attr("student_id");
    if(value !== undefined) {
        $('.div_student_' + value).fadeOut('300', function(){
            $('.div_student_' + value).remove();
            if(!$(".blocked_student")[0]) {
                $('.no_students_text').fadeIn('300');
            }
        });
        
    }
});

$(document).on("click", ".avatar-hover", function (event) {
    event.preventDefault();

    var avatar_id = $(this).attr("avatar_id");
    if (avatar_id === undefined) {
        return;
    }
    $(".current-avatar").attr("src", "assets/images/profile_images/" + avatar_id + ".png");
    $(".input_avatar_id").val(avatar_id);

});

$(document).on("input", "input.input_change", function (event) {
    event.preventDefault();
    $(".user_full_name").html($(".input_firstname").val() + " " + $(".input_surname").val());
});

$(document).on("click", ".settings_submit_password", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
    });
});

$(document).on("click", ".create_submit_info", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        if (ajax_data.reload) {
            setTimeout(function () {
                location.reload();
            }, 500);
        }
        show_status_bar("success", ajax_data.success);
    });
});