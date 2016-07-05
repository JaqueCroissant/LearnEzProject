var currently_changing_page = false;
var currently_submitting_form = false;
var currently_submitting_get = false;

var ajax_data;

$(document).ready(function () {
    
    // Load on startup.
        change_page("front");
    // Load on startup.
    
    $(document).on("click", ".change_page", function(event){
        if(currently_changing_page === false && $(this).attr("clickable") !== "false") {
            $(this).attr("clickable", false);
            event.preventDefault();
            var page = $(this).attr("page");
            var args = $(this).attr("args");
            change_page(page, args, $(this));
        }
    });
    
    $(document).on("click", ".submit_login", function(event){
        event.preventDefault();
        initiate_submit_form($(this), function() {
            alert(ajax_data.error);
        }, function() {
            location.reload();
        });
   });
   

   $(document).on("click", ".log_out", function(event){
       event.preventDefault();
       initiate_submit_get($(this), "login.php?logout=true", function() {
            alert(ajax_data.error);
        }, function() {
            location.reload();
        });
   });
   
    $(document).on("click", ".submit_edit_user_info", function(event){
        event.preventDefault
        initiate_submit_form($(this), function() {
            alert(ajax_data.error);
        }, function() {
        });
   });

    $(document).on("click", ".create_school", function(event){
        event.preventDefault();
        switch ($(this).attr("step")) {
            case "1": 
                $("#create_school_step").val($(this).attr("step"));
                initiate_submit_form($(this), function() {
                    alert(ajax_data.error); // fail function
                }, function() {
                    // start step 2 - success
                    
                    $("#step_one").addClass("hidden");
                    $("#step_two").removeClass("hidden");
                    $( "#datepicker" ).datepicker();
                });
                break;
            case "2":
                $("#create_school_step_2").val($(this).attr("step"));
                initiate_submit_form($(this), function() {
                    alert(ajax_data.error); // fail function
                }, function() {
                    // start step 3 - success
                    $("#step_two").addClass("hidden");
                    $("#step_three").removeClass("hidden");
                });
                break;
        }
        
        
        
    });
    
    function preload(arrayOfImages) {
        $(arrayOfImages).each(function(){
            $('<img/>')[0].src = this;
        });
    }

    $(function() {
        preload([
            'assets/images/loading_page.GIF'
        ]);
    });
    
});