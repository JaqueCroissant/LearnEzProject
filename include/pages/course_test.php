<?php
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';

$handler = new courseHandler();

if (!$handler->load_test(6)) {
    echo "<script>change_page('error', 'PAGE_NO_RIGHTS');</script>";
    die();
}
?>


<div id="iframe_content" style="overflow:hidden;" table_id="<?php echo isset($handler->test->id) ? $handler->test->id : "" ?>" current_slide="<?php echo (($handler->test->is_complete == 1) ? $handler->test->total_steps : (isset($handler->test->progress) ? $handler->test->progress : "1")); ?>">
    <div id="course_bar" class="course_bar widget" style="<?php echo "background-color: " . $handler->test->course_color . " !important;" ?>">      
        <div class="course_title pull-left"><?php echo "<h4>" . $handler->test->course_title . "<span class='p-v-sm zmdi zmdi-chevron-right fa-sm'></span>" . $handler->test->title . "</h4>" ?></div>
        <div class="btn-group pull-right course_navigation">
            <a href="javascript:void(0)" value="go_backwards" class="course_action course_go_back btn btn-default" title=<?php echo TranslationHandler::get_static_text("PREVIOUS") ?>><i class="fa fa-chevron-left"></i></a>
            <a href="javascript:void(0)" value="go_forwards" class="course_action course_go_for btn btn-default" title=<?php echo TranslationHandler::get_static_text("NEXT") ?>><i class="fa fa-chevron-right"></i></a>
        </div>
        <div class="course_slide_counter pull-right"></div>
    </div>
    <iframe scrolling="no" id="scaled-frame" class="course_iframe" src="<?php echo "../../LearnEZ/courses/" . $handler->test->path . "/index.php"; ?>"></iframe>
</div>
<div id="hidden_element" style="display:none"></div>

<script>
    
var can_be_clicked = false;
var slide_reached = parseInt(($("#iframe_content").attr("current_slide")));
var current_slide = parseInt(($("#iframe_content").attr("current_slide")));
var max_slide = 0;
var table_id = ($("#iframe_content").attr("table_id"));
    
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
        $("#iframe_content").height(740 * ratio + 60);
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
        $("#iframe_content").height(800);
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
                if (current_slide === max_slide) {
                    update_progress("test", 0, 1, 6);
                }
                else {
                    update_progress("test", current_slide, 0, 6);
                }
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
</script>

