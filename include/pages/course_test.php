<?php
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';

$handler = new courseHandler();

if (!$handler->get(6, "test")) {
    ErrorHandler::show_error_page();
    die();
}
?>


<div id="iframe_content" style="overflow:hidden;" table_id="<?php echo isset($handler->current_element->user_course_test_id) ? $handler->current_element->user_course_test_id : "" ?>" current_slide="<?php echo (($handler->current_element->is_complete == 1) ? $handler->current_element->total_steps : (isset($handler->current_element->progress) ? $handler->current_element->progress : "1")); ?>">
    <div class="course_bar widget" style="<?php echo "background-color: " . $handler->current_element->course_color . " !important;" ?>">      
        <div class="course_title pull-left"><?php echo "<span>" . $handler->current_element->course_title . "<span class='p-v-sm zmdi zmdi-chevron-right fa-sm'></span>" . $handler->current_element->title . "</span>" ?></div>
        <div class="btn-group pull-right course_navigation">
            <a href="javascript:void(0)" value="go_backwards" class="course_action course_go_back btn btn-default" title=<?php echo TranslationHandler::get_static_text("PREVIOUS") ?>><i class="fa fa-chevron-left"></i></a>
            <a href="javascript:void(0)" value="go_forwards" class="course_action course_go_for btn btn-default" title=<?php echo TranslationHandler::get_static_text("NEXT") ?>><i class="fa fa-chevron-right"></i></a>
        </div>
        <div class="course_slide_counter pull-right"></div>
    </div>
    <iframe scrolling="no" id="scaled-frame" class="course_iframe" src="<?php echo "../../LearnEZ/courses/" . $handler->current_element->path . "/index.php"; ?>"></iframe>
</div>
<div id="hidden_element" style="display:none"></div>

<script>
    
    
(function(){
    
    var can_be_clicked = false;
    var slide_reached = parseInt(($("#iframe_content").attr("current_slide")));
    var slide_reached_last = slide_reached;
    var current_slide = parseInt(($("#iframe_content").attr("current_slide")));
    var max_slide = 0;
    var update = true;
    var table_id = ($("#iframe_content").attr("table_id"));
    var time_since_last_save = 0;
    var interval_function;

    function resize(){
        var ratiow = $(".wrap").width() / 1024;
        var ratioh = ($(window).height() - 120) / 800;
        var ratio = ratiow > ratioh ? ratioh : ratiow;
        if (ratio < 1) {
            $("#scaled-frame").css({
                "transform" : "scale(" + ratio + ")",
                "-webkit-transform" : "scale(" + ratio + ")",
                "-ms-transform" : "scale(" + ratio + ")"
            });
            $("#scaled-frame").css({
                "margin-top" : -(740 - 740 * ratio) / 2, 
                "margin-left" : (-(1024 - 1024 * ratio) / 2) + ($(".wrap").width() - ratio * 1024) / 2
            });
            $("#iframe_content").height(740 * ratio + 80);
            $(".course_bar").width(1024 * ratio);
        } 
        else {
            $("#scaled-frame").css({
                "transform" : "scale(1)",
                "-webkit-transform" : "scale(1)",
                "-ms-transform" : "scale(1)"
            });
            $("#scaled-frame").css({
                "margin-top" : 0, 
                "margin-left" : ($(".wrap").width() - 1024) / 2
            });
            $("#iframe_content").height(820);
            $(".course_bar").width(1024);
        }
    }

    $(document).on("click", ".course_action", function(){
        if (can_be_clicked) {
            can_be_clicked = false;
            var window = document.getElementById("scaled-frame").contentWindow;
            var action = $(this).attr("value");
            switch (action){
                case "go_forwards" : 
                    if (current_slide !== slide_reached && current_slide !== max_slide) { 
                        window.cpAPIInterface.next(); 
                    } else {
                        $(".course_go_for").attr("disabled", true);
                    } 
                    break;
                case "go_backwards" : if (current_slide !== 1) {
                        window.cpAPIInterface.previous(); 
                    }
                    else {
                        $(".course_go_back").attr("disabled", true);
                    }
                    break;
                default : break;
            }
            can_be_clicked = true;
        }
    });


    $(document).ready(function() {    
        $("#iframe_content").css("margin", "0 auto");
        resize();
        $(window).on("resize", resize);
        var iframe_window = document.getElementById("scaled-frame").contentWindow;

        function update_init(){
            console.log("Attempting to save");
            if (slide_reached > slide_reached_last) {
                console.log("Saving");
                if (slide_reached === max_slide) {
                    update_progress("test", 0, 1, 6);
                }
                else {
                    update_progress("test", slide_reached, 0, 6);
                }
            }
            else {
                console.log("Save not needed");
            }
        }

        $(document).one("click.update_progress", ".change_page", function(){
            console.log("changing page");
            update_init();
            clearInterval(interval_function);
            $(window).unbind("unload.update_progress");
        });

        $(window).one("unload.update_progress", function(event){
            console.log("window unloaded");
            update_init();
            clearInterval(interval_function);
        });

        iframe_window.addEventListener("moduleReadyEvent", function(){
            can_be_clicked = true;
            max_slide = iframe_window.cpAPIInterface.getVariableValue("rdinfoSlideCount");
            $("#course_slide_counter").html("<p>" + current_slide + "/" +  max_slide + "</p>");
            if (current_slide === 1) {
                $(".course_go_back").attr("disabled", true);
            }
            if (current_slide === max_slide || current_slide === slide_reached) {

                $(".course_go_for").attr("disabled", true);
            }
            iframe_window.cpAPIEventEmitter.addEventListener("CPAPI_SLIDEENTER", function(event){
                current_slide = event.Data.slideNumber;
                if (current_slide > slide_reached) {
                    slide_reached = current_slide;
                }
                if (current_slide === 1) {
                    $(".course_go_back").attr("disabled", true);
                }
                else {
                    $(".course_go_back").attr("disabled", false);
                }
                if (current_slide === max_slide || current_slide === slide_reached) {
                    $(".course_go_for").attr("disabled", true);
                }
                else {
                    $(".course_go_for").attr("disabled", false);
                }
                $(".course_slide_counter").html("<b>" + current_slide + "/" +  max_slide + "</b>");
            });
            if (current_slide !== max_slide) {
                iframe_window.cpAPIInterface.setVariableValue("cpCmndGotoSlide", current_slide - 1);
                if (update) {
                   interval_function = setInterval(function(){
                        time_since_last_save++;
                        if (time_since_last_save >= 30) {
                            update_init();
                            slide_reached_last = slide_reached;
                            time_since_last_save = 0;
                        }
                    }, 1000);
                }
            }
            else {
                update = false;
            }
        });
    });

    function update_progress(type, progress, is_complete, action_id){
        initiate_submit_get($("#hidden_element"), "course.php?update_progress=" + type + "&progress=" + progress + "&is_complete=" + is_complete + "&table_id=" + table_id + "&action_id= "+ action_id, function () {
        }, function () {
            if(ajax_data.last_inserted_id !== null) {
                table_id = ajax_data.last_inserted_id;
            }

        });
    }
})();
</script>

