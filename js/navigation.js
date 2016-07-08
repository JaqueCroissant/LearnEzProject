function loading_page(is_loading) {
    if(is_loading === true) {
        $('#content_container').addClass("hidden");
        $('#loading_page').removeClass("hidden");
    } else {
        $('#loading_page').addClass("hidden");
        $('#content_container').removeClass("hidden");
    }
}

function load_breadcrumbs(display, pagename) {
    if(display === true) {
        pagename = pagename === undefined ? "front" : pagename;
        $("#content_breadcrumbs").load('include/template/breadcrumbs.php', {'url': false, 'pagename': pagename}, function() {} );
    }
}

function set_clickable(element) {
    if(element !== undefined) {
       element.removeAttr("clickable"); 
    }
}

function change_page(pagename, args, element) {
    currently_changing_page = true;
    $("#content_container").html("");
    $("#content_breadcrumbs").html("");
    var startTime = new Date().getTime();
    loading_page(true);
    var argument = args === undefined ? pagename : pagename + "&" + args;
    if (pagename != null) {
        var url = "include/ajax/change_page.php?page=" + argument;
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            async: false,
            success: function (data) {
                if (data.status_value === true) {
                    var page = "include/pages/" + data.pagename + ".php";
                    $("#content_container").load(page, {'url': false}, function() {
                        var elapsedTime = (new Date().getTime()) - startTime;
                        if(elapsedTime < 700) {
                            setTimeout(function() { 
                                loading_page(false); 
                                load_breadcrumbs(true, data.pagename);
                                set_clickable(element); 
                            }, (700-elapsedTime));
                        } else {
                            loading_page(false);
                            load_breadcrumbs(true, data.pagename);
                            set_clickable(element);
                        }
                    });
                } else {
                    $("#content_container").load('include/pages/front.php', {'url': false}, function() {
                        var elapsedTime = (new Date().getTime()) - startTime;
                        if(elapsedTime < 700) {
                            setTimeout(function() { 
                                loading_page(false); 
                                load_breadcrumbs(true);
                                set_clickable(element);
                            }, (700-elapsedTime));
                        } else {
                            loading_page(false);
                            load_breadcrumbs(true);
                            set_clickable(element);
                        }
                    });
                }
                
            },
            complete: function() {
                currently_changing_page = false;
                set_clickable(element);
            }
        });
    } else {
        $("#content_container").load('include/pages/front.php', {'url': false}, function() {
            var elapsedTime = (new Date().getTime()) - startTime;
            if(elapsedTime < 700) {
                setTimeout(function() { 
                    loading_page(false); 
                    load_breadcrumbs(true);
                    set_clickable(element); 
                }, (700-elapsedTime));
            } else {
                loading_page(false)
                load_breadcrumbs(true);
                set_clickable(element);
            }
        });
        currently_changing_page = false;
    }
}