function loading_page(is_loading) {
    if(is_loading === true) {
        $('#content_container').addClass("hidden");
        $('#loading_page').removeClass("hidden");
    } else {
        $('#loading_page').addClass("hidden");
        $('#content_container').removeClass("hidden");
    }
}

function load_breadcrumbs(breadcrumbs) {
    $("#content_breadcrumbs").html(breadcrumbs);
}

function set_clickable(element) {
    if(element !== undefined) {
       element.removeAttr("clickable"); 
    }
}

function change_page(pagename, args, element) {
    cursor_wait();
    currently_changing_page = true;
    $("#content_container").html("");
    $("#content_breadcrumbs").html("");
    var startTime = new Date().getTime();
    loading_page(true);
    pagename = pagename === undefined ? "front" : pagename;
    args = args === undefined ? "" : args;
    var url = "include/ajax/change_page.php?page=" + pagename + "&step=" + args;
    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        async: true,
        success: function (data) {
            var page = "include/pages/" + data.pagename + ".php?step=" + args;
            $("#content_container").load(page, {'url': false}, function() {
                var elapsedTime = (new Date().getTime()) - startTime;
                if(elapsedTime < 700) {
                    setTimeout(function() { 
                        loading_page(false);
                        load_breadcrumbs(data.breadcrumbs);
                        set_clickable(element); 
                    }, (700-elapsedTime));
                } else {
                    loading_page(false);
                    load_breadcrumbs(data.breadcrumbs);
                    set_clickable(element);
                }
                remove_cursor_wait();
            });
        },
        complete: function() {
            currently_changing_page = false;
            set_clickable(element);
        }
    });
}