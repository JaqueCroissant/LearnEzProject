var currently_changing_page = false;
var currently_submitting_form = false;
var currently_submitting_get = false;

var ajax_data;

$(document).ready(function () {
    
    // Load on startup.
        change_page("front");
    // Load on startup end.
    
    $('.change_page').click(function (event) {
        
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
        })
   });
   
   $(document).on("click", ".log_out", function(event){
       event.preventDefault();
       initiate_submit_get($(this), "login.php?logout=true", function() {
            alert(ajax_data.error);
        }, function() {
            location.reload();
        })
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