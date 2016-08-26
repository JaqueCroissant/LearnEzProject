$(document).ready(function () {
    notification_beat();
    start_notification_beat(30);
    
    var can_be_clicked = false;
    var progress_reached = 0;
    var progress_reached_last = 0;
    var current_progress = 0;
    var max_progress = 0;
    var update = false;
    var done = false;
    var table_id = 0;
    var action_id = 0;
    var task = "";
    var time_since_last_save = 0;
    var interval_function;
    var hidden = true;
    var paused = false;
    var ratio = 1;
    var mute = false;
    var time_between_saves = 15;
    var cookie_expiration_time = 1;
    var course_clickable = true;
    var time_before_close_test = 5000;
    var time_before_close_lecture = 2000;
    
    if ($.cookie("current_task") !== undefined) {
        
        var data = JSON.parse($.cookie("current_task"));
        task = data.task;
        mute = data.mute;
        action_id = parseInt(data.data);
        $.ajax({
            type: "POST",
            url: "include/ajax/course.php?" + (task === "test" ? "play_test=1&test_id=" : "play_lecture=1&lecture_id=") + data.data,
            dataType: "json",
            async: false,
            complete: function(a_data) {
                open(a_data.responseJSON, false);
            }
        });
    }
    
    function first_slide_enter(){
        current_progress === 1 ? $(".course_go_back").attr("disabled", true) : $(".course_go_back").attr("disabled", false);
        current_progress === max_progress || current_progress === progress_reached ? $(".course_go_for").attr("disabled", true) : $(".course_go_for").attr("disabled", false);
        $(".course_slide_counter").html("<b>" + current_progress + "/" +  max_progress + "</b>");
        document.getElementById("scaled-frame").contentWindow.cpAPIEventEmitter.removeEventListener("CPAPI_SLIDEENTER", first_slide_enter);
        document.getElementById("scaled-frame").contentWindow.cpAPIEventEmitter.addEventListener("CPAPI_SLIDEENTER", slide_enter);
        setTimeout(function(){
            if (current_progress !== max_progress) {
                document.getElementById("scaled-frame").contentWindow.cpAPIInterface.setVariableValue("cpCmndGotoSlide", current_progress - 1);
            }
        }, 1);
    }
    
    function slide_enter(event){
        current_progress = event.Data.slideNumber;
        if (current_progress > progress_reached) {
            progress_reached = current_progress;
        }
        set_test_buttons();
        if (current_progress >= max_progress) {
            update_init();
            update = false;
            done = true;
            clearInterval(interval_function);
            $(".course_action").attr("disabled", true);
            setTimeout(function(){
                close();
            }, time_before_close_test);
        }
        
    }
    
    function set_test_buttons(){
        current_progress === 1 ? $(".course_go_back").attr("disabled", true) : $(".course_go_back").attr("disabled", false);
        current_progress === max_progress || current_progress === progress_reached ? $(".course_go_for").attr("disabled", true) : $(".course_go_for").attr("disabled", false);
        $(".course_mute").attr("disabled", false);
        $(".course_unmute").attr("disabled", false);
        $(".course_slide_counter").html("<b>" + current_progress + "/" +  max_progress + "</b>");
    }
    
    function set_lecture_buttons(){
        $(".course_lecture_button").attr("disabled", false);
        update_lecture_counter();
        if (current_progress >= progress_reached - 2) {
            $(".course_continue").attr("disabled", true);
        }
        else {
            $(".course_continue").attr("disabled", false);
        }
    }
    
    function set_menu_buttons(){
        task === "test" ? set_test_buttons() : set_lecture_buttons();
        $(".course_default").attr("disabled", false);
    }
    
    function switch_play_pause(){
        var player = $(".course_video")[0];
        if(player.ended) {
            current_progress = $(player)[0].currentTime > max_progress ? max_progress : Math.floor($(player)[0].currentTime);
            if (current_progress > progress_reached) {
                progress_reached = current_progress;
            }
            update_init();
            update = false;
            done = true;
            clearInterval(interval_function);
            setTimeout(function(){
                close();
            }, time_before_close_lecture);
            $(player).prop("controls", false);
            $(".course_pause").hide();
            $(".course_play").show();
            $(".course_action").attr("disabled", true);
        }
        else if (player.paused) {
            $(".course_pause").show();
            $(".course_play").hide();
            paused = false;
            player.play();
        }
        else {
            $(".course_pause").hide();
            $(".course_play").show();
            paused = true;
            player.pause();
        }
    }
    
    function start_lecture_interval(){
        if (update) {
            time_since_last_save = 0;
            interval_function = setInterval(function(){
                time_since_last_save++;
                if (time_since_last_save >= time_between_saves) {
                    update_init();
                    progress_reached_last = progress_reached;
                    time_since_last_save = 0;
                }
            }, 1000);
        }
    }
    
    function start_test_interval(){
        if (update) {
            time_since_last_save = 0;
            interval_function = setInterval(function(){
                 time_since_last_save++;
                 if (time_since_last_save >= time_between_saves) {
                     update_init();
                     progress_reached_last = progress_reached;
                     time_since_last_save = 0;
                 }
             }, 1000);
        }
    }
    
    function update_lecture_counter(){
        $(".course_slide_counter").html("<p>" 
            + (Math.floor(current_progress / 60) < 10 ? "0" + Math.floor(current_progress / 60) : Math.floor(current_progress / 60))  
            + ":" + (current_progress % 60 < 10 ? "0" + current_progress % 60 : current_progress % 60) 
            + "/" + (Math.floor(max_progress / 60) < 10 ? "0" + Math.floor(max_progress / 60) : Math.floor(max_progress / 60)) 
            + ":" + (max_progress % 60 < 10 ? "0" + max_progress % 60 : max_progress % 60 + "</p>"));  
    }
    
    function init_iframe_window(){
        var iframe_window = document.getElementById("scaled-frame").contentWindow;
        iframe_window.addEventListener("moduleReadyEvent", function(){
            hidden ? iframe_window.cpAPIInterface.pause() : iframe_window.cpAPIInterface.play();
            check_mute();
            current_progress = (parseInt(current_progress) === max_progress ? 1 : current_progress);
            iframe_window.cpAPIEventEmitter.addEventListener("CPAPI_SLIDEENTER", first_slide_enter);
            
            $("#course_slide_counter").html("<p>" + current_progress + "/" +  max_progress + "</p>");
            if (current_progress === 1) {
                $(".course_go_back").attr("disabled", true);
            }
            if (current_progress === max_progress || current_progress === progress_reached) {
                $(".course_go_for").attr("disabled", true);
            }
        });
    }

    $(document).on("click", ".course_action", function(){
        if (can_be_clicked && $(this).attr("disabled") !== "disabled") {
            can_be_clicked = false;
            var window = document.getElementById("scaled-frame").contentWindow;
            var action = $(this).attr("value");
            switch (action){
                case "go_forwards" : 
                    if (current_progress !== progress_reached && current_progress !== max_progress) { 
                        window.cpAPIInterface.next();
                    } else {
                        $(".course_go_for").attr("disabled", true);
                    } 
                    break;
                case "go_backwards" : 
                    if (current_progress !== 1) {
                        window.cpAPIInterface.previous();
                    }
                    else {
                        $(".course_go_back").attr("disabled", true);
                    }
                    break;
                case "quit" :
                    close();
                    break;
                case "minimize" :
                    $(".backdrop").trigger("click");
                    break;
                case "mute" :
                    switch_mute();
                    break;
                case "continue" :
                    $(".course_video")[0].currentTime = progress_reached >= max_progress ? $(".course_video")[0].duration : progress_reached - 1;
                    break;
                case "play" :
                    if( $(".course_video")[0].ended) {
                        $(".course_video")[0].play();
                        paused = false;
                        $(".course_pause").show();
                        $(".course_play").hide();
                    }
                    else {
                        switch_play_pause();
                    }
                    break;
                case "pause" :
                    switch_play_pause();
                    break;
                case "repeat" :
                    $(".course_video")[0].currentTime = 0;
                    current_progress = 0;
                    update_lecture_counter();
                    if ($(".course_video")[0].paused) {
                        switch_play_pause();
                    }
                    break;
                default : break;
            }
            can_be_clicked = true;
        }
    });

    function update_init(){
        if (update && progress_reached > progress_reached_last) {
            if (progress_reached >= max_progress) {
                update_progress(task, max_progress, 1);
            }
            else {
                update_progress(task, progress_reached, 0);
            }
        }
    }
    
    function check_mute(){
        if (mute) {
            switch_mute();
        }
    }
    
    function switch_mute(){
        var c_window = document.getElementById("scaled-frame").contentWindow;
        var current = c_window.cpAPIInterface.getVolume();
        if (current === 100) {
            c_window.cpAPIInterface.setVolume(0);
            $(".course_mute").hide();
            $(".course_unmute").show();
            mute = true;
        }
        else {
            c_window.cpAPIInterface.setVolume(100);
            $(".course_unmute").hide();
            $(".course_mute").show();
            mute = false;
        }
        var temp = JSON.parse($.cookie("current_task"));
        temp.mute = mute;
        $.cookie("current_task", JSON.stringify(temp), {path: "/", expires:cookie_expiration_time});
    }
    
    function close(){
        hide_backdrop();
        update_init();
        $(".test_content").fadeOut(500, function(){
            $(this).hide();
            $.removeCookie("current_task", {path:"/"});
            reload_page_content("course_show");
        });
        $("#scaled-frame").remove("html");
        $("#scaled-frame").attr("src", "");
        $(".course_video").attr("src", "");
        $(window).unbind("unload.update_progress");
    }
    
    function open(data, open){
        done = false;
        current_progress = parseInt(data.current_progress) < 5 && task === "lecture" ? 0 : parseInt(data.current_progress) ;
        progress_reached = parseInt(data.current_progress);
        progress_reached_last = parseInt(data.current_progress);
        max_progress = task === "test" ? parseInt(data.max_progress) : parseInt(data.max_progress) - 1;
        current_progress = current_progress >= max_progress ? max_progress : current_progress;
        update = !(progress_reached >= max_progress + 1);
        table_id = data.user_course_table_id === null ? 0 : parseInt(data.user_course_table_id);
        switch_icon();
        course_player_init(data, 1000);
        if (open) {
            $(".course_return").trigger("click");
        }
        else {
            $(".course_return").show();
        }
        $(window).one("unload.update_progress", function(){
            clearInterval(interval_function);
        });
    }
    
    function switch_icon(){
        if(task === "test"){
            if($(".course_return_icon").hasClass("zmdi-movie")) {
                $(".course_return_icon").toggleClass("zmdi-movie zmdi-graduation-cap");
            }
        }
        else {
            if($(".course_return_icon").hasClass("zmdi-graduation-cap")) {
                $(".course_return_icon").toggleClass("zmdi-graduation-cap zmdi-movie");
            }
        }
    }

    function update_progress(type, progress, is_complete){
        initiate_submit_get($("#hidden_element"), "course.php?update_progress=" + type + "&progress=" + progress + "&is_complete=" + is_complete + "&table_id=" + table_id + "&action_id= "+ action_id, function () {
        }, function () {
            if(ajax_data.last_inserted_id !== null) {
                table_id = ajax_data.last_inserted_id;
            }
        });
    }
    
    function resize(){
        if (task === "test") {
            var ratiow = ($(window).width() - 20) / 1024;
            var ratioh = ($(window).height() - 60) / 740;
            ratio = ratiow > ratioh ? ratioh : ratiow;
            $("#scaled-frame").css({
                "transform" : "scale(" + ratio + ")",
                "-webkit-transform" : "scale(" + ratio + ")",
                "-ms-transform" : "scale(" + ratio + ")"
            });
            $("#scaled-frame").css({
                "margin-top" : -(740 - 740 * ratio) / 2 - 1, 
                "margin-left" : -(1024 - 1024 * ratio) / 2
            });
            if (!hidden) {
                $("#iframe_content").css({
                    "height" : 740 * ratio + 40,
                    "width" : 1024 * ratio,
                    "margin-left" : ($(window).width() - ratio * 1024) / 2,
                    "margin-bottom" : ($(window).height() - 60 - ratio * 740) / 2
                });
            }
            else {
                $("#iframe_content").css({
                    "height" : 740 * ratio + 40,
                    "width" : 1024 * ratio,
                    "margin-left" : ($(window).width() - ratio * 1024) / 2,
                    "margin-bottom" : -(740 * ratio + 60)
                });
            }
        }
        else {
            var width = $(".course_video")[0].videoWidth;
            var height = $(".course_video")[0].videoHeight;
            width = width === 0 ? 1280 : width;
            height = height === 0 ? 720 : height;
            var ratiow = ($(window).width() - 20) / width;
            var ratioh = ($(window).height() - 60) / height;
            ratio = ratiow > ratioh ? ratioh : ratiow;
            if (!hidden) {
                $("#iframe_content").css({
                    "height" : height * ratio + 40,
                    "width" : width * ratio,
                    "margin-left" : ($(window).width() - ratio * width) / 2,
                    "margin-bottom" : ($(window).height() - 60 - ratio * height) / 2
                });
            }
            else {
                $("#iframe_content").css({
                    "height" : height * ratio + 40,
                    "width" : width * ratio,
                    "margin-left" : ($(window).width() - ratio * width) / 2,
                    "margin-bottom" : -(height * ratio + 60)
                });
            }
        }
    }
    
    function hide_backdrop(){
        hidden = true;
        $(".backdrop").animate({opacity:0}, 500, "easeOutQuad", function(){
            $(".backdrop").hide();
            course_clickable = true;
        });
        clearInterval(interval_function);
    }
        
    $(document).on("click", ".backdrop", function(){      
        if (!hidden && !done) {
            hide_backdrop();
            $(".course_action").attr("disabled", true);
            can_be_clicked = false;
            $("#iframe_content").animate({"margin-bottom":-($("#iframe_content").height() + 10)}, 700, "easeInOutQuad", function(){
                $(this).hide();
                $(".course_return").css("display", "block");
            });
            if(task === "test") {
                document.getElementById("scaled-frame").contentWindow.cpAPIInterface.pause();
            }
            else {
                $(".course_video")[0].pause();
            }
            clearInterval(interval_function);
        }
    });

    $(document).on("click", ".course_return", function(){
        if (hidden) {
            course_clickable = false;
            $(".course_return").hide();
            $("#iframe_content").css({"display" : "block", "opacity" : 1}); 
            resize();
            $(".course_slide_counter").html("");
            $(".course_action").attr("disabled", true);
            $("#iframe_content").animate({"margin-bottom": (($(window).height() - 60 - ratio * (task === "test" ? 740 : ($(".course_video")[0].videoHeight === 0 ? 720 : $(".course_video")[0].videoHeight))) / 2)}, 700, "easeInOutQuad", function() {
                set_menu_buttons();
                hidden = false;
                can_be_clicked = true;
            });
            $(".backdrop").show();
            $(".backdrop").animate({opacity:1}, 500, "easeOutCubic");
            if (task === "test") {
                start_test_interval();
            }
            else {
                if ($(".course_video")[0].readyState === 4) {
                    setTimeout(function(){
                        if(!paused) {
                            $(".course_video")[0].play();
                        }
                        start_lecture_interval();
                    }, 700);
                    
                }
                else {
                    $(".course_video").one("loadeddata", function(){
                        start_lecture_interval();
                        $(".course_video")[0].play();
                    });
                }
            }
        }
    });
    
    function course_player_init(data, wait){
        $(".course_window_test").show();
        $(".course_player_test_title").html(data.action_title);
        $(".course_player_course_title").html(data.course_title);
        if (task === "test") {
            $(".course_test_menu").show();
            $(".course_lecture_menu").hide();
            $(".course_iframe").show();
            $(".course_video").hide();
            setTimeout(function(){
                $(".course_iframe").attr("src", "courses/tests/" + data.path + "/index.php");
                $(".course_iframe").one("load", function(){
                    $(window).on("resize", resize);
                    init_iframe_window();
                });
            }, wait);
        }
        else {
            $(".course_test_menu").hide();
            $(".course_lecture_menu").show();
            $(".course_iframe").hide();
            $(".course_video").show();
            $(".course_video").prop("controls", true);
            setTimeout(function(){
                $(".course_video").attr("src", "courses/lectures/" + data.path);
                $(".course_video").one("loadeddata", function(){
                    var width = $(this)[0].videoWidth;
                    var height = $(this)[0].videoHeight;
                    var ratiow = ($(window).width() - 20) / width;
                    var ratioh = ($(window).height() - 60) / height;
                    ratio = ratiow > ratioh ? ratioh : ratiow;
                    $("#iframe_content").animate({
                        "height" : height * ratio + 40,
                        "width" : width * ratio,
                        "margin-left" : ($(window).width() - ratio * width) / 2,
                        "margin-bottom" : ($(window).height() - 60 - ratio * height) / 2
                    }, 500, "easeInOutQuad");
                    $(window).on("resize", resize);
                    $(".course_video")[0].currentTime = current_progress >= max_progress ? 0 : current_progress;
                    update_lecture_counter();
                    });
            }, wait);
        }
        
    }
    
    (function() {
        $(".course_video").bind("ended", function(){
            switch_play_pause();
        });
        $(".course_video")[0].ontimeupdate = function(){
            current_progress = $(this)[0].currentTime > max_progress ? max_progress : Math.floor($(this)[0].currentTime);
            update_lecture_counter();
            if (current_progress >= progress_reached - 2) {
                $(".course_continue").attr("disabled", true);
            }
            else {
                $(".course_continue").attr("disabled", false);
            }
            if (current_progress > progress_reached) {
                progress_reached = current_progress;
            }
        };
    })();

    $(document).on("click", ".play_test", function(){
        if (course_clickable) {
            course_clickable = false;
            if($(this).attr("element_id") === undefined) {
                return;
            }
            if ($.cookie("current_task") === undefined || !(JSON.parse($.cookie("current_task")).data === $(this).attr("element_id") && JSON.parse($.cookie("current_task")).task === "test")) {
                if ($.cookie("current_task") !== undefined) {
                    update_init();
                    $(".course_video").attr("src", "");
                    $(".course_video").load();
                }

                action_id = $(this).attr("element_id");
                initiate_submit_get($(this), "course.php?play_test=1&test_id="+$(this).attr("element_id"), function () {
                    show_status_bar("error", ajax_data.error);
                }, function () {
                    $.cookie("current_task", '{"task" : "test", "data" : "' + action_id + '", "mute" : "false"}', {expires: cookie_expiration_time, path: "/"});
                    mute = false;
                    task = "test";
                    open(ajax_data, true);
                });
            }
        }
        else {
            $(".course_return").trigger("click");
        }
    });

    $(document).on("click", ".play_lecture", function(){
        if (course_clickable) {
            course_clickable = false;
            if($(this).attr("element_id") === undefined) {
                return;
            }
            if ($.cookie("current_task") === undefined || !(JSON.parse($.cookie("current_task")).data === $(this).attr("element_id") && JSON.parse($.cookie("current_task")).task === "lecture")) {

                if ($.cookie("current_task") !== undefined) {
                    update_init();
                    $(".course_video").attr("src", "");
                }

                action_id = $(this).attr("element_id");
                initiate_submit_get($(this), "course.php?play_lecture=1&lecture_id="+$(this).attr("element_id"), function () {
                    show_status_bar("error", ajax_data.error);
                }, function () {
                    $.cookie("current_task", '{"task" : "lecture", "data" : "' + action_id + '", "mute" : "false"}', {expires: cookie_expiration_time, path: "/"});
                    mute = false;
                    paused = false;
                    task = "lecture";
                    open(ajax_data, true);
                });
            }
            else {
                $(".course_return").trigger("click");
            }
        }
    });
});





