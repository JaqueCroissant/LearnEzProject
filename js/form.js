function submit_form(form_id, url, element) {
    $("#" + form_id).submit();
    $.ajax({
        type: "POST",
        url: "include/ajax/" + url,
        dataType: "json",
        async: false,
        data: $("#" + form_id).serialize(),
        error: function(data) {
            alert(data);
        },
        complete: function(data) {
            alert(JSON.stringify(data));
            currently_submitting_form = false;
            $(element).removeAttr("clickable");
            ajax_data = $.parseJSON(JSON.stringify(data.responseJSON));
        }
    });
}

function initiate_submit_form(element, fail_function, success_function) {
    if(currently_submitting_form === false && $(element).attr("clickable") !== "false") {
        $(element).attr("clickable", false);
        currently_submitting_form = true;

        form_id = $(element).closest("form").attr("id");
        url = $(element).closest("form").attr("url");
        alert("1");
        submit_form(form_id, url, $(element));
        alert(ajax_data);
        alert(JSON.stringify(ajax_data));
        if(ajax_data.status_value === true) {
            success_function();
        } else {
            fail_function();
        }
    }
}


function submit_get(url, element) {
    $.ajax({
        type: "POST",
        url: "include/ajax/" + url,
        dataType: "json",
        async: false,
        complete: function(data) {
            currently_submitting_get = false;
            $(element).removeAttr("clickable");
            ajax_data = $.parseJSON(JSON.stringify(data.responseJSON));
        }
    });
}

function initiate_submit_get(element, url, fail_function, success_function) {
    if(currently_submitting_get === false && $(element).attr("clickable") !== "false") {
        $(element).attr("clickable", false);
        currently_submitting_get = true;

        submit_get(url, $(element));

        if(ajax_data.status_value === true) {
            success_function();
        } else {
            fail_function();
        }
    }
}