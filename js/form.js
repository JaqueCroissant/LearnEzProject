function submit_form(form_id, url, element, fail_function, success_function) {
    cursor_wait();
    $("#" + form_id).submit();
    $.ajax({
        type: "POST",
        url: "include/ajax/" + url,
        dataType: "json",
        async: true,
        data: $("#" + form_id).serialize(),
        complete: function(data) {
            currently_submitting_form = false;
            $(element).removeAttr("clickable");
            ajax_data = $.parseJSON(JSON.stringify(data.responseJSON));
            fail_success(fail_function, success_function);
            remove_cursor_wait();
        }
    });
}

function initiate_submit_form(element, fail_function, success_function) {
    if(currently_submitting_form === false && $(element).attr("clickable") !== "false") {
        $(element).attr("clickable", false);
        currently_submitting_form = true;
        form_id = $(element).closest("form").attr("id");
        url = $(element).closest("form").attr("url");
        submit_form(form_id, url, $(element), fail_function, success_function);

    }
}

function custom_submit_form(form_id, url, args, element, fail_function, success_function) {
    cursor_wait();
    $("#" + form_id).submit();
    args = args === undefined ? "" : args;
    $.ajax({
        type: "POST",
        url: "include/ajax/" + url + args,
        dataType: "json",
        async: true,
        data: $("#" + form_id).serialize(),
        complete: function(data) {
            currently_submitting_form = false;
            $(element).removeAttr("clickable");
            ajax_data = $.parseJSON(JSON.stringify(data.responseJSON));
            fail_success(fail_function, success_function);
            remove_cursor_wait();
        }
    });
}

function initiate_custom_submit_form(element, fail_function, success_function, args, form_id, url) {
    if(currently_submitting_form === false && $(element).attr("clickable") !== "false") {
        $(element).attr("clickable", false);
        currently_submitting_form = true;
        form_id = form_id === undefined ? $(element).closest("form").attr("id") : form_id;
        var form = $("#" + form_id);
        url = url === undefined ? form.attr("url") : url;
        custom_submit_form(form_id, url, args, $(element), fail_function, success_function);
    }
}


function submit_get(url, element, fail_function, success_function) {
    cursor_wait();
    $.ajax({
        type: "POST",
        url: "include/ajax/" + url,
        dataType: "json",
        async: true,
        complete: function(data) {
            currently_submitting_get = false;
            $(element).removeAttr("clickable");
            ajax_data = $.parseJSON(JSON.stringify(data.responseJSON));
            fail_success(fail_function, success_function);
            remove_cursor_wait();
        }
    });
}

function initiate_submit_get(element, url, fail_function, success_function) {
    if(currently_submitting_get === false && $(element).attr("clickable") !== "false") {
        $(element).attr("clickable", false);
        currently_submitting_get = true;
        submit_get(url, $(element), fail_function, success_function);
    }
}

function fail_success(fail_function, success_function) {
    if(ajax_data.status_value === true) {
        success_function();
    } else {
        fail_function();
    }
}