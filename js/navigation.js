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
       element.removeAttr("clickable"); 
    }
}

function change_page(pagename, step, args, element) {
    cursor_wait();
    currently_changing_page = true;
    
    $("#content_container").add($("#content_breadcrumbs")).fadeTo(300, 0, function() {
        content_hidden = true;
    });
   
    var startTime = new Date().getTime();
    
    pagename = pagename === undefined ? "front" : pagename;
    step = step === undefined ? "" : step;
    args = args === undefined ? "" : args;
    var url = "include/ajax/change_page.php?page=" + pagename + "&step=" + step + args;
    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        async: true,
        success: function (data) {
            var page = "include/pages/" + data.pagename + ".php?step=" + step + args;
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
            });
            
        },
        complete: function() {
            currently_changing_page = false;
            set_clickable(element);
        }
    });
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