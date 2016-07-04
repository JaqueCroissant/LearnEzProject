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
            alert("lol2");
        });
        alert("JEG ER TIL SIDST");
        return false;
   });

    $(document).on("click", ".create_school", function(event){
        event.preventDefault();
        $("#create_school_step").val($("#create_school_step_one_button").attr("step"));
        initiate_submit_form($(this), function() {
            alert(ajax_data.error); // fail function
        }, function() {
            // start step 2 - success
            // change div
            alert("lol");
        });
    });
    
    function showDropDown(element){
        var listElement = element.parentNode.getElementsByTagName('ul').item(0);
        if(listElement.getAttribute('style')=="display:block;"){
            listElement.setAttribute('style','display:none;');
        }else{
            listElement.setAttribute('style','display:block;');
        }
    }
    
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