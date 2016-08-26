
$(document).on("click", ".submit_edit_user_info", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        $(".username").html(ajax_data.full_name);
        if(ajax_data.avatar_id === null || ajax_data.avatar_id === undefined) {
            ajax_data.avatar_id = "default.png";
        }
        $(".current-avatar-image").attr("src", "assets/images/profile_images/" + ajax_data.avatar_id);
        $(".current-avatar").attr("src", "assets/images/profile_images/" + ajax_data.avatar_id);
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

$(document).on("click", ".upload_profile_image", function (event) {
    var form = $(this).closest("form");
    event.preventDefault();
    var formData = new FormData(form[0]);
    $.ajax({
        url: 'include/ajax/settings.php?step=upload_profile_image',
        type: 'POST',
        data: formData,
        dataType: "json",
        async: false,
        complete: function (data) {
            ajax_data = $.parseJSON(JSON.stringify(data.responseJSON));
            if (ajax_data.status_value) {
                show_status_bar("success", ajax_data.success);
                update_profile_images();
            } else {
                show_status_bar("error", ajax_data.error);
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
});

var current_profile_image_id;

$(document).on({
    mouseenter: function () {
        $(this).find(".delete_profile_image_style").removeClass("hidden");
        $(this).css({opacity: 1});
    },
    mouseleave: function () {
        $(this).find(".delete_profile_image_style").addClass("hidden");

        if ($(this).attr("profile_image_id") !== current_profile_image_id && current_profile_image_id !== undefined) {
            $(this).css({opacity: 0.5});
        }
    }
}, ".profile_image_element");

$(document).on("click", ".delete_profile_image", function (event) {
    event.stopPropagation();
    var form = $(this).closest("form");
    var profile_image_id = $(this).attr("profile_image_id");
    event.preventDefault();
    initiate_submit_get($(this), "settings.php?delete_profile_image=1&profile_image_id=" + profile_image_id, function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        update_profile_images(true);
        if ($(".input_avatar_id").val() === profile_image_id) {
            $(".active_profile_image").addClass('hidden');
            $(".profile_image_element").css({opacity: 1});
            $(".current-avatar-image").attr("src", "assets/images/profile_images/default.png");
            $(".current-avatar").attr("src", "assets/images/profile_images/default.png");
            current_profile_image_id = undefined;
        }
    });
});

function update_profile_images(is_true) {
    var url = "settings.php?get_profile_images" + (current_profile_image_id !== undefined ? "&selected_profile_image=" + current_profile_image_id : "");
    initiate_submit_get($(this), url, function () {
        if(is_true) {
            $(".profile-images-placeholder").html("<div style='width:100%;text-align:center;'>" + ajax_data.error + "</div>");
        }
    }, function () {
        if (ajax_data.profile_images !== undefined) {
            $(".profile-images-placeholder").html(ajax_data.profile_images);
        }
    });
}

$(document).on("click", ".profile_image_element", function (event) {
    if ($(this).attr("profile_image_id") === current_profile_image_id) {
        $(".active_profile_image").addClass('hidden');
        $(".profile_image_element").css({opacity: 1});
        current_profile_image_id = undefined;
        $(".input_avatar_id").val(0);
        return;
    }

    current_profile_image_id = $(this).attr("profile_image_id");
    $(".input_avatar_id").val(current_profile_image_id);
    var current_profile_image = $(this).find(".active_profile_image");
    current_profile_image.removeClass("hidden");
    $(".active_profile_image").not(current_profile_image).addClass('hidden');
    $(".profile_image_element").not($(this)).css({opacity: 0.5});
});