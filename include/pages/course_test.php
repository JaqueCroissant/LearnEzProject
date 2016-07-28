<?php
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';

$handler = new courseHandler();
$handler->load_test(6);
?>


<div id="iframe_content" style="overflow:hidden;" max_slide="<?php echo $handler->test->total_steps; ?>" current_slide="<?php echo ((isset($handler->test->is_complete) && $handler->test->is_complete == 1) ? $handler->test->total_steps : (isset($handler->test->progress) ? $handler->test->progress : "1")); ?>">
    <iframe scrolling="no" id="scaled-frame" class="course_iframe" src=<?php echo "../../LearnEZ/courses/" . $handler->test->path . "/index.php"; ?>></iframe>
    <div id="course_bottombar" class="course_bar widget" style="color:<?php echo $handler->test->course_color ?>">
        <div class="course_slide_counter pull-left"></div>
        <div class="btn-group pull-right course_navigation">
            <a href="javascript:void(0)" value="go_backwards" class="course_action course_go_back btn btn-default" title=<?php echo TranslationHandler::get_static_text("PREVIOUS") ?>><i class="fa fa-chevron-left"></i></a>
            <a href="javascript:void(0)" value="go_forwards" class="course_action course_go_for btn btn-default" title=<?php echo TranslationHandler::get_static_text("NEXT") ?>><i class="fa fa-chevron-right"></i></a>
        </div>
    </div>
</div>

<script>
    
var can_be_clicked = false;
var slide_reached = (int)($("#iframe_content").attr("current_slide"));
var current_slide = (int)($("#iframe_content").attr("current_slide"));
var max_slide = (int)($("#iframe_content").attr("max_slide"));
    
function resize(){
    var ratio = $(".wrap").width() / 1024;
    if (ratio < 1) {
        $("#scaled-frame").css({
            "transform" : "scale(" + ratio + ")",
            "-webkit-transform" : "scale(" + ratio + ")",
            "-ms-transform" : "scale(" + ratio + ")"
        });
        $("#scaled-frame").css({
            "margin-top" : -(740 - 740 * ratio) / 2, 
            "margin-left" : -(1024 - 1024 * ratio) / 2
        });
        $("#course_bottombar").css("margin-top", -(750 - 740 * ratio) / 2);
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
        $("#course_bottombar").css("margin-top", -5);
        $("#iframe_content").height(800);
        $(".course_bar").width(1024);
    }
}

$(document).on("click", ".course_action", function(event){
    if (can_be_clicked) {
        can_be_clicked = false;
        var window = document.getElementById("scaled-frame").contentWindow;
        var action = $(this).attr("value");
        switch (action){
            case "go_forwards" : if (current_slide !== slide_reached && current_slide !== max_slide) { window.cpAPIInterface.next(); } break;
            case "go_backwards" : if (current_slide !== 1) {window.cpAPIInterface.previous(); } break;
            default : break;
        }
        setTimeout(function(){
            can_be_clicked = true;
        }, 500);
    }
});

$(document).ready(function() {
    resize();
    $("#iframe_content").css("margin", "0 auto");
    $(window).on("resize", resize);
    
    var window = document.getElementById("scaled-frame").contentWindow;
    window.addEventListener("moduleReadyEvent", function(){
        can_be_clicked = true;
        current_slide = window.cpAPIInterface.getCurrentSlideIndex();
        max_slide = window.cpAPIInterface.getVariableValue("rdinfoSlideCount");
        $("#course_slide_counter").html("<p>" + current_slide + "/" +  max_slide + "</p>");
        if (current_slide === 1) {
            $(".course_go_back").attr("disabled", true);
        }
        if (current_slide === max_slide || current_slide === slide_reached) {
            $(".course_go_for").attr("disabled", true);
        }
        
        window.cpAPIEventEmitter.addEventListener("CPAPI_SLIDEENTER", function(event){
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
    });
});
</script>

