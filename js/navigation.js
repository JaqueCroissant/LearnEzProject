function loading_page(is_loading) {
    if(is_loading === true) {
        $('#content_container').addClass("hidden");
        $('#loading_page').removeClass("hidden");
    } else {
        $('#loading_page').addClass("hidden");
        $('#content_container').removeClass("hidden");
    }
}

function set_clickable(element) {
    if(element !== undefined) {
        try {
            element.removeAttr("clickable"); 
        }
        catch(e){

        }
    }
}

function change_page(pagename, step, args, element, scroll_to_top, complete_function) {
    cursor_wait();
    currently_changing_page = true;
    scroll_to_top = scroll_to_top === undefined;
    $("#content_container").add($("#content_breadcrumbs")).fadeTo(300, 0, function() {
        content_hidden = true;
        $(".popover").popover("hide");
    });
   
    var startTime = new Date().getTime();
    
    pagename = pagename === undefined ? "front" : pagename;
    step = step === undefined ? "" : step;
    args = args === undefined ? "" : args;
    var url = "include/ajax/change_page.php?page=" + pagename + "&step=" + step + args;
    var redirect_token = pagename + (step !== "" ? "_" + step : "");
    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        async: true,
        success: function (data) {
            is_error_page = data.pagename === "error" ? true : false;
            if(data.error_code !== undefined) {
                var page = "include/pages/" + data.pagename + ".php?redirect_token="+data.pagename+"&step=" + data.error_code;
            } else {
                var page = "include/pages/" + data.pagename + ".php?redirect_token="+redirect_token+"&step=" + step + args;
            }
            $.get(page, {'url': false}, function (e) {
                var elapsedTime = (new Date().getTime()) - startTime;
                if(elapsedTime < 700) {
                    setTimeout(function() { 
                        set_clickable(element); 
                    }, (700-elapsedTime));
                } else {
                    set_clickable(element);
                }
                append_content(e, data.breadcrumbs);
                clearUrl();
            });
            if (data.lang_id !== undefined) {
                current_lang_id = data.lang_id;
            }
        },
        complete: function() {
            currently_changing_page = false;
            set_clickable(element);
            $(".popover").popover("hide");
            if(scroll_to_top) {
                window.scrollTo(0, 0);
            }
            complete_function();
        }
    });
}

function change_page_from_overlay(pagename, step, args, element, scroll_to_top) {
    change_page(pagename, step, args, element, scroll_to_top, function() {setTimeout(function() { $(".login_overlay").fadeOut(500);}, 300)}); 
}

var currentIteration = 0, totalIterations = 10;
function append_content(content, breadcrumbs) {
    if(content_hidden === true) {
        currentIteration = 0;
        content_hidden = false;
        remove_cursor_wait();
        $("#content_container").html(content);
        $("#content_breadcrumbs").html(breadcrumbs);
        $("#content_container").add($("#content_breadcrumbs")).fadeTo(300, 1);
        return;
    }
    currentIteration++;
    if(currentIteration < totalIterations){
        setTimeout(function() { append_content(content, breadcrumbs); }, 300 );
    }
}