var currently_changing_page = false;
var currently_submitting_form = false;
var currently_submitting_get = false;
var content_hidden = false;

var ajax_data;

$(document).ready(function () {
    // Load on startup.
    initial_page_load();
    // Load on startup.
    
    
    // global functions
    $(document).on("click", ".change_page", function(event){
        if(currently_changing_page === false && $(this).attr("clickable") !== "false" && !$(this).attr('disabled')) {
            $(this).attr("clickable", false);
            event.preventDefault();
            var page = $(this).attr("page");
            var args = $(this).attr("args");
            var extra_args = $(this).attr("extra_args");
            change_page(page, args, extra_args, $(this)); 
        }
    });
    
    $(document).on("click", ".check_all", function(event){
        event.preventDefault();
        var form = $(this).attr("target_form");
        var checkboxes = $("#" + form).find(':checkbox');
        if($(this).attr("checked")) {
            checkboxes.prop('checked', false);
            $(this).removeAttr("checked");
            $(this).find("i").first().toggleClass('fa-square-o fa-check-square-o');
        } else {
            checkboxes.prop('checked', true);
            $(this).attr("checked", true);
            $(this).find("i").first().toggleClass('fa-check-square-o fa-square-o');
        }
    });
    //
    
    // login / logout
    $(document).on("click", ".submit_login", function(event){
        event.preventDefault();
        initiate_submit_form($(this), function() {
            alert(ajax_data.error);
        }, function() {
            reload_page();
        });
   });
   

   $(document).on("click", ".log_out", function(event){
       event.preventDefault();
       initiate_submit_get($(this), "login.php?logout=true", function() {
            alert(ajax_data.error);
        }, function() {
            $.removeCookie("current_page");
            reload_page();
        });
   });
   //
   
   // mail
   $(document).on("click", ".assign_mail_folder", function(event){
        event.preventDefault();
        if ($("#" + $(this).attr("target_form") + " input:checkbox:checked").length > 0) {
            initiate_custom_submit_form($(this), function() {
                alert(ajax_data.error);
            }, function() {
                if(ajax_data.mails_removed !== undefined) {
                    ajax_data.mails_removed.forEach(function(entry) {
                        $(".mail_number_" + entry).fadeOut(500);
                    }); 
                }
            }, $(this).attr("args"), $(this).attr("target_form"));
        }
   });
   //
   
   // edit user info
    $(document).on("click", ".submit_edit_user_info", function(event){
        event.preventDefault();
        initiate_submit_form($(this), function() {
            alert(ajax_data.error);
        }, function() {
        });
   });

   $(document).on("click", ".settings_submit_password", function(event){
        event.preventDefault();
        initiate_submit_form($(this), function() {
            alert(ajax_data.error);
        }, function() {
        });
   });

   $(document).on("click", ".reset_pass_submit_email2", function(event){
        event.preventDefault
        initiate_submit_form($(this), function() {
            alert(ajax_data.error);
        }, function() {
            location.reload();
        });
   });
   //

    // school
    $(document).on("click", ".create_school", function(event){
        event.preventDefault();
        switch ($(this).attr("step")) {
            case "1": 
                $("#create_school_hidden_field_step_1").attr("value", $(this).attr("step"));
                initiate_submit_form($(this), function() {
                    alert(ajax_data.error); // fail function
                }, function() {
                    // start step 2 - success
                    $("#step_one").addClass("hidden");
                    $("#step_two").removeClass("hidden");
                    $("#step_one_progress").addClass("progress-bar-success");
                    $("#step_two_progress").switchClass("progress-bar-inactive", "progress-bar");
                    $("#school_subscription_end").datepicker();
                });
                break;
            case "2":
                $("#create_school_hidden_field_step_2").val($(this).attr("step"));
                initiate_submit_form($(this), function() {
                    alert(ajax_data.error); // fail function
                }, function() {
                    // start step 3 - success
                    $("#step_two").addClass("hidden");
                    $("#step_three").removeClass("hidden");
                    $("#step_two_progress").addClass("progress-bar-success");
                    $("#step_three_progress").switchClass("progress-bar-inactive", "progress-bar");
                });
                break;
        }
    });
    //
    
    
    // global functions
    function preload(arrayOfImages) {
        $(arrayOfImages).each(function(){
            $('<img/>')[0].src = this;
        });
    }
    
    function initial_page_load() {
        var page_reload = $.cookie("page_reload");
        $.removeCookie("page_reload");
        
        var pagename = page_reload === "true" ? "front" :  $.cookie("current_page") !== undefined ? $.cookie("current_page") : "front";
        var page_arguments = page_reload === "true" ? "" :  $.cookie("current_page_arguments") !== undefined ? $.cookie("current_page_arguments") : "";
        change_page(pagename, page_arguments);
    }
    
    function reload_page() {
        var date = new Date();
        date.setTime(date.getTime() + (60 * 1000));
        $.cookie("page_reload", "true", { expires: 10 });
        $.removeCookie("current_page");
        $.removeCookie("current_page_arguments");
        location.reload();
    }

    $(function() {
        preload([
            'assets/images/loading_page.GIF'
        ]);
    });
    //
});

function cursor_wait()
{
    var elements = $(':hover');
    if (elements.length) {
        elements.last().addClass('cursor-wait');
    }
    $('html').off('mouseover.cursorwait').on('mouseover.cursorwait', function(e) {
        $(e.target).addClass('cursor-wait');
    });
}

function remove_cursor_wait() {
    $('html').off('mouseover.cursorwait');
    $('.cursor-wait').removeClass('cursor-wait');
}

