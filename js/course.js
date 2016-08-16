var current_ajax;
var test_file_name;
var is_uploading = false;
var is_aborted = false;
var has_uploaded = false;
var current_upload_type;
var module_ready = false;

function reset_variables() {
    current_ajax = undefined;
    test_file_name = undefined;
    is_uploading = false;
    is_aborted = false;
    has_uploaded = false;
    current_upload_type = undefined;
    module_ready = false;
}

function reset_progress() {
    has_uploaded = false;
    is_uploading = true;
    is_aborted = false;

    if (current_upload_type === "test") {
        $(".cancel_test_upload").attr("disabled", false);
        $(".cancel_test_upload").val("Annuller");
        $(".test_progress").fadeTo(500, 1);
        $(".test_progress_value").html("Uploader data");
        $(".test_progress_bar").addClass("active");
    } else {
        $(".cancel_lecture_upload").attr("disabled", false);
        $(".cancel_lecture_upload").val("Annuller");
        $(".lecture_progress").fadeTo(500, 1);
        $(".lecture_progress_value").html("Uploader data");
        $(".lecture_progress_bar").addClass("active");
    }
}

$(document).on("click", ".upload_lecture", function (event) {
    if ($("#thumbnail_lecture").val() === undefined || $("#thumbnail_lecture").val() === "") {
        show_status_bar("error", "Du skal vælge en fil.");
        return;
    }

    event.preventDefault();
    delete_lecture(function () {
        upload_lecture($(".upload_lecture"))
    });
});

$(document).on("click", ".upload_test", function (event) {
    if ($("#thumbnail_test").val() === undefined || $("#thumbnail_test").val() === "") {
        show_status_bar("error", "Du skal vælge en fil.");
        return;
    }

    event.preventDefault();
    delete_test(function () {
        upload_test($(".upload_test"))
    });
});

function upload_lecture(event) {
    if (event.attr("disabled") !== "disabled") {

        current_upload_type = "lecture";
        var form = event.closest("form");
        event.attr("disabled", true);
        reset_progress();

        var formData = new FormData(form[0]);
        current_ajax = $.ajax({
            xhr: function () {
                var xhr = $.ajaxSettings.xhr();

                xhr.upload.onprogress = function (evt) {
                    if (evt.loaded >= evt.total) {
                        is_uploading = false;
                        $(".upload_lecture").attr("disabled", true);
                        $(".lecture_progress_bar").css("width", "100%");
                        return;
                    }
                    $(".lecture_progress_bar").css("width", (evt.loaded / evt.total * 100) + "%");
                    $(".lecture_progress_value").html("Uploader data: " + Math.round(evt.loaded / evt.total * 100) + "%");
                };
                return xhr;
            },
            url: 'include/ajax/course.php?step=upload_lecture',
            type: 'POST',
            data: formData,
            dataType: "json",
            async: true,
            complete: function (data) {
                if (is_aborted) {
                    return;
                }

                ajax_data = $.parseJSON(JSON.stringify(data.responseJSON));
                $("#thumbnail_lecture").val("");
                $(".upload_lecture").attr("disabled", false);
                if (ajax_data.status_value) {
                    $(".lecture_progress_value").html("Færdig");
                    test_file_name = ajax_data.file_name;
                    $(".test_progress .progress-bar").removeClass("active");
                    $(".cancel_lecture_upload").val("Slet fil");
                    $(".cancel_lecture_upload").attr("disabled", false);
                    $("#lecture_total_length").val(ajax_data.file_duration);
                    $("#lecture_file_name").val(ajax_data.file_name);
                    has_uploaded = true;
                } else {
                    show_status_bar("error", ajax_data.error);
                    $(".lecture_progress").fadeTo(1000, 0, function () {
                        $(".lecture_progress_bar").css("width", "0");
                        $(".cancel_lecture_upload").attr("disabled", true);
                    });
                }
                is_uploading = false;
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
}

function upload_test(event) {
    if (event.attr("disabled") !== "disabled") {

        current_upload_type = "test";
        var form = event.closest("form");
        event.attr("disabled", true);
        reset_progress();

        var formData = new FormData(form[0]);
        current_ajax = $.ajax({
            xhr: function () {
                var xhr = $.ajaxSettings.xhr();

                xhr.upload.onprogress = function (evt) {
                    if (evt.loaded >= evt.total) {
                        is_uploading = false;
                        $(".test_progress_value").html("Udpakker filer");
                        $(".upload_test").attr("disabled", true);
                        $(".test_progress_bar").css("width", "100%");
                        return;
                    }
                    $(".test_progress_bar").css("width", (evt.loaded / evt.total * 100) + "%");
                    $(".test_progress_value").html("Uploader data: " + Math.round(evt.loaded / evt.total * 100) + "%");
                };
                return xhr;
            },
            url: 'include/ajax/course.php?step=upload_test',
            type: 'POST',
            data: formData,
            dataType: "json",
            async: true,
            complete: function (data) {
                if (is_aborted) {
                    return;
                }

                ajax_data = $.parseJSON(JSON.stringify(data.responseJSON));
                if (ajax_data.status_value) {
                    test_file_name = ajax_data.file_name;
                    $(".test_progress_value").html("Loader testen");
                    load_test();

                } else {
                    show_status_bar("error", ajax_data.error);
                    $(".test_progress").fadeTo(1000, 0, function () {
                        $(".upload_test").attr("disabled", false);
                        $(".test_progress_bar").css("width", "0");
                    });
                }
                is_uploading = false;
                $(".cancel_test_upload").attr("disabled", true);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
}

$(document).on("click", ".cancel_test_upload", function (event) {
    cancel_upload();
    delete_test();
});

$(document).on("click", ".cancel_lecture_upload", function (event) {
    cancel_upload();
    delete_lecture();
});

function cancel_upload() {
    if (!is_uploading) {
        return;
    }

    is_aborted = true;
    current_ajax.abort();
    if (current_upload_type === "test") {
        $(".test_progress").fadeTo(1000, 0, function () {
            $(".upload_test").attr("disabled", false);
            $(".test_progress_bar").css("width", "0");
            show_status_bar("success", "Upload afbrudt.");
        });
    } else {
        $(".lecture_progress").fadeTo(1000, 0, function () {
            $(".upload_lecture").attr("disabled", false);
            $(".lecture_progress_bar").css("width", "0");
            show_status_bar("success", "Upload afbrudt.");
        });
    }
}

function delete_lecture(func) {
    if (has_uploaded === false || test_file_name === undefined) {
        if (func !== undefined) {
            func();
        }
        return;
    }

    has_uploaded = false;
    $(".lecture_progress_value").html("Sletter upload");
    $(".lecture_progress_bar").addClass("active");
    console.log(test_file_name);
    initiate_submit_get($(this), "media.php?step=delete_lecture&file_name=" + test_file_name,
            function () {
                show_status_bar("error", ajax_data.error);
                if (func !== undefined) {
                    func();
                }
            }, function () {
        $(".lecture_progress_bar").removeClass("active");
        $(".lecture_progress").fadeTo(1000, 0, function () {
            $(".upload_lecture").attr("disabled", false);
            $(".lecture_progress_bar").css("width", "0");
            show_status_bar("success", ajax_data.success);
            if (func !== undefined) {
                func();
            }
        });
    });
}

function delete_test(func) {
    if (has_uploaded === false || test_file_name === undefined) {
        if (func !== undefined) {
            func();
        }
        return;
    }

    has_uploaded = false;
    $(".test_progress_value").html("Sletter upload");
    $(".test_progress_bar").addClass("active");
    initiate_submit_get($(this), "media.php?step=delete_test&file_name=" + test_file_name,
            function () {
                show_status_bar("error", ajax_data.error);
                if (func !== undefined) {
                    func();
                }
            }, function () {
        $(".test_progress_bar").removeClass("active");
        $(".test_progress").fadeTo(1000, 0, function () {
            $(".upload_test").attr("disabled", false);
            $(".test_progress_bar").css("width", "0");
            show_status_bar("success", ajax_data.success);
            if (func !== undefined) {
                func();
            }
        });
    });
}

function load_test() {
    $("#test_player").attr("src", "courses/tests/" + test_file_name + "/index.php");
    $("#test_player").one("load", function () {
        var iframe_window = document.getElementById("test_player").contentWindow;
        setTimeout(function () {
            if (!module_ready) {
                $("#test_player").remove("html");
                $("#test_player").attr("src", "");
                $(".test_progress .progress-bar").removeClass("active");
                $(".test_progress_value").html("Fejl");
                setTimeout(function () {
                    $(".test_progress").fadeTo(500, 0, function () {
                        $(".upload_test").attr("disabled", false);
                    });
                }, 1000);
                initiate_submit_get($(this), "media.php?step=delete_test&file_name=" + test_file_name, function () {
                }, function () {
                });
            }
        }, 10000);
        iframe_window.addEventListener("moduleReadyEvent", function () {
            $(".test_progress_value").html("Indsamler data");
            module_ready = true;
            $("#test_total_steps").val(iframe_window.cpAPIInterface.getVariableValue("rdinfoSlideCount"));
            $("#test_file_name").val(ajax_data.file_name);
            setTimeout(function () {
                show_status_bar("success", ajax_data.success);
                $(".test_progress_value").html("Færdig");
                $(".test_progress .progress-bar").removeClass("active");
                $(".upload_test").attr("disabled", false);
            }, 500);

            has_uploaded = true;
            $(".cancel_test_upload").val("Slet fil");
            $(".cancel_test_upload").attr("disabled", false);
            $("#thumbnail_test").val("");
        });
    });
}

$(document).on("click", ".upload_thumbnail", function (event) {
    var form = $(this).closest("form");
    event.preventDefault();
    var formData = new FormData(form[0]);
    $.ajax({
        url: 'include/ajax/course.php?step=upload_thumbnail',
        type: 'POST',
        data: formData,
        dataType: "json",
        async: false,
        complete: function (data) {
            ajax_data = $.parseJSON(JSON.stringify(data.responseJSON));
            if (ajax_data.status_value) {
                show_status_bar("success", ajax_data.success);
                update_thumbnails();
            } else {
                show_status_bar("error", ajax_data.error);
            }
        },
        cache: false,
        contentType: false,
        processData: false
    });
});

$(document).on({
    mouseenter: function () {
        $(this).find(".delete_thumbnail").removeClass("hidden");
        $(this).find(".set_default_thumbnail").removeClass("hidden");
        $(this).css({opacity: 1});
    },
    mouseleave: function () {
        $(this).find(".delete_thumbnail").addClass("hidden");
        var set_default_thumbnail = $(this).find(".set_default_thumbnail");

        if (set_default_thumbnail.attr("default_thumbnail") !== "1") {
            set_default_thumbnail.addClass("hidden");
        }

        if ($(this).attr("thumbnail_id") !== current_thumbnail_id && current_thumbnail_id !== undefined) {
            $(this).css({opacity: 0.5});
        }
    }
}, ".thumbnail_element");

$(document).on("click", ".thumbnail_element", function (event) {
    if ($(this).attr("thumbnail_id") === current_thumbnail_id) {
        $(".active_thumbnail").addClass('hidden');
        $(".thumbnail_element").css({opacity: 1});
        current_thumbnail_id = undefined;
        $(".thumbnail_picked").val(0);
        return;
    }

    current_thumbnail_id = $(this).attr("thumbnail_id");
    $(".thumbnail_picked").val(current_thumbnail_id);
    var current_thumbnail = $(this).find(".active_thumbnail");
    current_thumbnail.removeClass("hidden");
    $(".active_thumbnail").not(current_thumbnail).addClass('hidden');
    $(".thumbnail_element").not($(this)).css({opacity: 0.5});
});


$(document).on("click", ".delete_thumbnail", function (event) {
    event.stopPropagation();
    var form = $(this).closest("form");
    var thumbnail_id = $(this).attr("thumbnail_id");
    event.preventDefault();
    initiate_submit_get($(this), "course.php?delete_thumbnail=1&thumbnail_id=" + thumbnail_id, function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        update_thumbnails();
        if ($(".thumbnail_picked").val() === thumbnail_id) {
            $(".active_thumbnail").addClass('hidden');
            $(".thumbnail_element").css({opacity: 1});
            current_thumbnail_id = undefined;
        }
    });
});

$(document).on("click", ".set_default_thumbnail", function (event) {
    event.stopPropagation();
    var form = $(this).closest("form");
    var thumbnail_id = $(this).attr("thumbnail_id");
    event.preventDefault();
    initiate_submit_get($(this), "course.php?set_default_thumbnail=1&thumbnail_id=" + thumbnail_id, function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        update_thumbnails();
    });
});

function update_thumbnails() {
    var url = "course.php?get_thumbnails" + (current_thumbnail_id !== undefined ? "&selected_thumbnail=" + current_thumbnail_id : "");
    initiate_submit_get($(this), url, function () {
    }, function () {
        if (ajax_data.thumbnails !== undefined) {
            $(".thumbnail-placeholder").html(ajax_data.thumbnails);
        }
    });
}

$(document).on("click", ".add_translation", function (event) {
    var form = $(this).closest("form");
    var value = form.find('#language').find(":selected").val();
    var name = form.find('#language').find(":selected").text();
    var title = form.find('.title_text').text();
    var description = form.find('.description_text').text();
    var translation = form.find('.translation_text').text();
    var type = form.find('.translation_type').text();

    if (value !== undefined && name !== undefined && !form.find(".translation_" + value)[0]) {
        var block = $('<div class="translation_' + value + ' translation_element"><div class="user-card m-b-sm student_20" style="padding: 8px !important;background:#f0f0f1;"><div class="media"><div class="media-body"><input type="hidden" name="language_id[]" value="' + value + '"/><div class="accordion translation_' + type + '' + value + '" id="accordion" role="tablist" aria-multiselectable="false"><div class=""><div class="panel-heading" role="tab" id="heading-' + type + '' + value + '"><a class="accordion-toggle collapsed" style="padding: 5px 0px 0px 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-' + type + '' + value + '" aria-expanded="false" aria-controls="collapse-' + type + '' + value + '"><label for="textarea' + value + '" style="cursor:pointer">' + name + ' ' + translation + '</label><i class="fa acc-switch"></i><i class="zmdi zmdi-hc-lg zmdi-delete pull-right remove_translation" translation_id="' + value + '" style="margin-top:1px;cursor:pointer;"></i></a></div><div id="collapse-' + type + '' + value + '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-' + type + '' + value + '" aria-expanded="false" style="height: 0px;"><div class="panel-body" style="padding: 5px 10px 10px 10px !important;"><label for="title" style="margin-bottom:0px !important;">' + title + '</label><input type="text" id="title" name="title[]" placeholder="" class="form-control"><label for="description" style="margin: 10px 0px 0px 0px !important;">' + description + '</label><input type="text" id="description" name="description[]" placeholder="" class="form-control"></div></div></div></div></div></div></div></div>').hide().fadeIn(300);
        if (form.find('.no_translations_text').is(":visible")) {
            form.find('.no_translations_text').fadeOut('300', function () {
                form.find(".translations").append(block);
            });
        } else {
            form.find(".translations").append(block);
        }
    }
});


$(document).on("click", ".remove_translation", function (event) {
    var form = $(this).closest("form");
    var value = $(this).attr("translation_id");
    if (value !== undefined) {
        form.find('.translation_' + value).remove();
        if (!form.find(".translation_element")[0]) {
            form.find('.no_translations_text').fadeIn('300');
        }
    }
});

$(document).on("click", ".submit_create_course", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        reset_variables();
        change_page("course_administrate", "create_course");
    });
});

$(document).on("click", ".submit_update_course", function (event) {
    event.preventDefault();
    initiate_submit_form($(this), function () {
        show_status_bar("error", ajax_data.error);
    }, function () {
        show_status_bar("success", ajax_data.success);
        change_page("find_course");
    });
});

$(document).on("change", ".add_courses", function (event) {
    var form = $(this).closest("form");
    var os_id = $(this).find("option:selected").val();
    event.preventDefault();
    initiate_submit_get($(this), "course.php?get_courses=1&os_id=" + os_id, function () {
        show_status_bar("error", ajax_data.error);
        form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
        form.find("#sort_order").empty();
    }, function () {
        if (ajax_data.courses !== "") {
            form.find(".sort_order").attr("style", "height:auto;opacity:1;");
            form.find("#sort_order").html(ajax_data.courses);
            var search = form.find("#sort_order");
            search.select2();
        } else {
            form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
            form.find("#sort_order").empty();
        }
    });
});

$(document).on("change", ".add_lectures", function (event) {
    var form = $(this).closest("form");
    var course_id = $(this).find("option:selected").val();
    event.preventDefault();
    initiate_submit_get($(this), "course.php?get_lectures=1&course_id=" + course_id, function () {
        show_status_bar("error", ajax_data.error);
        form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
        form.find("#sort_order").empty();
    }, function () {
        if (ajax_data.lectures !== "") {
            form.find(".sort_order").attr("style", "height:auto;opacity:1;");
            form.find("#sort_order").html(ajax_data.lectures);
            var search = form.find("#sort_order");
            search.select2();
        } else {
            form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
            form.find("#sort_order").empty();
        }
    });
});

$(document).on("change", ".add_tests", function (event) {
    var form = $(this).closest("form");
    var course_id = $(this).find("option:selected").val();
    event.preventDefault();
    initiate_submit_get($(this), "course.php?get_tests=1&course_id=" + course_id, function () {
        show_status_bar("error", ajax_data.error);
        form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
        form.find("#sort_order").empty();
    }, function () {
        if (ajax_data.tests !== "") {
            form.find(".sort_order").attr("style", "height:auto;opacity:1;");
            form.find("#sort_order").html(ajax_data.tests);
            var search = form.find("#sort_order");
            search.select2();
        } else {
            form.find(".sort_order").attr("style", "height:0px;opacity:0;margin-top:-10px !important;");
            form.find("#sort_order").empty();
        }
    });
});

