$(document).ready(function () {
    notification_beat();
    start_notification_beat(30);
    
    var can_be_clicked = false;
    var progress_reached = 0;
    var progress_reached_last = 0;
    var current_progress = 0;
    var max_progress = 0;
    var update = false;
    var table_id = 0;
    var time_since_last_save = 0;
    var interval_function;
    var hidden = true;
    var ratio = 1;
    var mute = false;
    
    if ($.cookie("current_task") !== undefined) {
        
        var data = JSON.parse($.cookie("current_task"));
        mute = data.mute;
        $.ajax({
            type: "POST",
            url: "include/ajax/course.php?play_test=1&test_id=" + data.data,
            dataType: "json",
            async: false,
            complete: function(a_data) {
                var response = a_data.responseJSON;
                open(response, false);
            }
        });
        console.log("muted | " + mute);
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
                if (update) {
                   interval_function = setInterval(function(){
                        time_since_last_save++;
                        if (time_since_last_save >= 30) {
                            update_init();
                            progress_reached_last = progress_reached;
                            time_since_last_save = 0;
                        }
                    }, 1000);
                }
            }
            else {
                update = false;
            }
        }, 1);
    }
    
    function slide_enter(event){
        console.log("entering " + event.Data.slideNumber);
        console.log("table id: " + table_id);
        current_progress = event.Data.slideNumber;
        $.cookie("current_progress", (current_progress === max_progress ? 1 : current_progress), {path: "/"});
        if (current_progress > progress_reached) {
            progress_reached = current_progress;
        }
        current_progress === 1 ? $(".course_go_back").attr("disabled", true) : $(".course_go_back").attr("disabled", false);
        current_progress === max_progress || current_progress === progress_reached ? $(".course_go_for").attr("disabled", true) : $(".course_go_for").attr("disabled", false);
        $(".course_slide_counter").html("<b>" + current_progress + "/" +  max_progress + "</b>");
    }
    
    function init_iframe_window(){
        console.log("frame init");
        $(window).on("resize", resize);
        var iframe_window = document.getElementById("scaled-frame").contentWindow;
        iframe_window.addEventListener("moduleReadyEvent", function(){
            hidden ? iframe_window.cpAPIInterface.pause() : iframe_window.cpAPIInterface.play();
            check_mute();
            current_progress = (parseInt(current_progress) === max_progress ? 1 : current_progress);
            iframe_window.cpAPIEventEmitter.addEventListener("CPAPI_SLIDEENTER", first_slide_enter);
            can_be_clicked = true;
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
        if (can_be_clicked) {
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
                default : break;
            }
            can_be_clicked = true;
        }
    });

    function update_init(){
        console.log("Attempting to save");
        if (progress_reached > progress_reached_last) {
            console.log("Saving");
            if (progress_reached === max_progress) {
                update_progress("test", 0, 1, 6);
            }
            else {
                update_progress("test", progress_reached, 0, 6);
            }
        }
        else {
            console.log("Save not needed");
        }
        var temp = JSON.parse($.cookie("current_task"));
        temp.mute = mute;
        $.cookie("current_task", JSON.stringify(temp), {path: "/"});
        
    }
    
    function check_mute(){
        if (mute) {
            switch_mute();
        }
    }
    
    function switch_mute(){
        var c_window = document.getElementById("scaled-frame").contentWindow;
        var current = c_window.cpAPIInterface.getVolume();
        var i = $(".course_mute").find("i");
        if (current === 100) {
            c_window.cpAPIInterface.setVolume(0);
            i.toggleClass("zmdi-volume-up zmdi-volume-off");
            mute = true;
        }
        else {
            c_window.cpAPIInterface.setVolume(100);
            i.toggleClass("zmdi-volume-off zmdi-volume-up");
            mute = false;
        }
    }
    
    function close(){
        hide_backdrop();
        update_init();
        $(".test_content").fadeOut(500, function(){
            $(this).hide();
            $.removeCookie("current_progress", {path:"/"});
            $.removeCookie("current_task", {path:"/"});
        });
        $("#scaled-frame").remove("html");
        $("#scaled-frame").attr("src", "");
        $(window).unbind("unload.update_progress");
    }
    
    function open(data, open){
        current_progress = parseInt($.cookie("current_progress"));
        progress_reached = parseInt(data.current_progress);
        progress_reached_last = parseInt(data.current_progress);
        max_progress = parseInt(data.max_progress);
        table_id = parseInt(data.user_course_table_id);
        course_player_init(data, 500);
        if (open) {
            $(".course_return").trigger("click");
        }
        else {
            $(".course_return").show();
        }
        $(window).one("unload.update_progress", function(event){
            console.log("window unloaded");
            update_init();
            clearInterval(interval_function);
        });
    }

    function update_progress(type, progress, is_complete, action_id){
        initiate_submit_get($("#hidden_element"), "course.php?update_progress=" + type + "&progress=" + progress + "&is_complete=" + is_complete + "&table_id=" + table_id + "&action_id= "+ action_id, function () {
        }, function () {
            if(ajax_data.last_inserted_id !== null) {
                table_id = ajax_data.last_inserted_id;
            }
        });
    }
    
    function resize(){
        var ratiow = ($(window).width() - 20) / 1024;
        var ratioh = ($(window).height() - 60) / 740;
        ratio = ratiow > ratioh ? ratioh : ratiow;
        if (!hidden) {
            $("#scaled-frame").css({
                "transform" : "scale(" + ratio + ")",
                "-webkit-transform" : "scale(" + ratio + ")",
                "-ms-transform" : "scale(" + ratio + ")"
            });
            $("#scaled-frame").css({
                "margin-top" : -(740 - 740 * ratio) / 2 - 1, 
                "margin-left" : -(1024 - 1024 * ratio) / 2
            });
            $("#iframe_content").css({
                "height" : 740 * ratio + 40,
                "width" : 1024 * ratio,
                "margin-left" : ($(window).width() - ratio * 1024) / 2,
                "margin-bottom" : ($(window).height() - 40 - ratio * 740) / 2 - 10
            });
        }
        else {
            $("#scaled-frame").css({
                "transform" : "scale(" + ratio + ")",
                "-webkit-transform" : "scale(" + ratio + ")",
                "-ms-transform" : "scale(" + ratio + ")"
            });
            $("#scaled-frame").css({
                "margin-top" : -(740 - 740 * ratio) / 2 - 1, 
                "margin-left" : -(1024 - 1024 * ratio) / 2
            });
            $("#iframe_content").css({
                "height" : 740 * ratio + 40,
                "width" : 1024 * ratio,
                "margin-left" : ($(window).width() - ratio * 1024) / 2,
                "margin-bottom" : -(740 * ratio + 50)
            });
        }
    }
    
    function hide_backdrop(){
        hidden = true;
        $(".backdrop").animate({opacity:0}, 500, "easeOutQuad", function(){
            $(".backdrop").hide();
        });
        clearInterval(interval_function);
    }
        
    $(document).on("click", ".backdrop", function(){      
        if (!hidden) {
            hide_backdrop();
            $("#iframe_content").animate({"margin-bottom":-($("#iframe_content").height() + 10), bottom:0}, 700, "easeInOutQuad", function(){
                can_be_clicked = false;
                $(this).hide();
                $(".course_return").css("display", "block");
            });
            document.getElementById("scaled-frame").contentWindow.cpAPIInterface.pause();
        }
    });

    $(document).on("click", ".course_return", function(){
        if (hidden) {
            $(".course_return").hide();
            $("#iframe_content").css({"display" : "block", "opacity" : 1}); 
            resize();
            $("#iframe_content").animate({"margin-bottom": (($(window).height() - 40 - ratio * 740) / 2 -10), bottom:10}, 700, "easeInOutQuad", function() {
                hidden = false;
                can_be_clicked = true;
            });
            $(".backdrop").show();
            $(".backdrop").animate({opacity:1}, 500, "easeOutCubic");
        }
    });
    
    function course_player_init(data, wait){
        $(".course_window_test").show();
        $(".course_player_test_title").html(data.test_title);
        $(".course_player_course_title").html(data.course_title);
        setTimeout(function(){
            $(".course_iframe").attr("src", "courses/tests/" + data.path + "/index.php");
            $(".course_iframe").one("load", function(){
                init_iframe_window();
            });
        }, wait);
    }

    $(document).on("click", ".play_test", function(){
        if($(this).attr("element_id") === undefined) {
            return;
        }
        if ($.cookie("current_task") === undefined || JSON.parse($.cookie("current_task")).data !== $(this).attr("element_id")) {
            console.log("getting new test");
            initiate_submit_get($(this), "course.php?play_test=1&test_id="+$(this).attr("element_id"), function () {
                show_status_bar("error", ajax_data.error);
            }, function () {
                $.cookie("current_task", '{"task" : "test", "data" : "' + 6 + '", "mute" : "false"}', {expires: 1, path: "/"});
                $.cookie("current_progress", (ajax_data.current_progress === ajax_data.max_progress ? 1 : ajax_data.current_progress), {expires: 1, path: "/"});
                mute = false;
                open(ajax_data, true);
            });
        }
        else {
            $(".course_return").trigger("click");
        }
    });

    $(document).on("click", ".play_lecture", function(){
    });
});





