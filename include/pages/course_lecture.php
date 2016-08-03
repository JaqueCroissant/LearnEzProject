<?php 
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';

$courseHandler = new courseHandler();

if (!$courseHandler->get(6, "test")) {
    ErrorHandler::show_error_page();
    die();
}
?>

<div class="test_content">
   <div class="course_bar widget" style="<?php echo "background-color: " . $courseHandler->current_element->course_color . " !important;width:95%;max-width:1024px;" ?>">      
        <div class="course_title pull-left"><?php echo "<span>" . $courseHandler->current_element->course_title . "<span class='p-v-sm zmdi zmdi-chevron-right fa-sm'></span>" . $courseHandler->current_element->title . "</span>" ?></div>
        <div class="btn-group pull-right course_navigation">
            <a href="javascript:void(0)" value="go_backwards" class="course_action course_go_back btn btn-default" title=<?php echo TranslationHandler::get_static_text("PREVIOUS") ?>><i class="fa fa-chevron-left"></i></a>
            <a href="javascript:void(0)" value="go_forwards" class="course_action course_go_for btn btn-default" title=<?php echo TranslationHandler::get_static_text("NEXT") ?>><i class="fa fa-chevron-right"></i></a>
        </div>
        <div class="course_slide_counter pull-right"></div>
    </div>
    <div class="course_player">
        <video class="course_video" src="../../LearnEZ/courses/lectures/lecture_1.mp4"></video>
    </div>
</div>

<script>
(function(){
    
    $(".course_video")[0].addEventListener('loadedmetadata', function(e){
        $(window).on("resize", resize);
    });
    
    function resize(){
        var height = $(".course_video")[0].videoHeight;
        var width = $(".course_video")[0].videoWidth;
        var ratiow = $(".wrap").width() / width;
        var ratioh = ($(window).height() - 145) / height;
        var ratio = ratiow > ratioh ? ratioh : ratiow;
        $(".course_video").height(height * ratio);
        $(".course_video").width(width * ratio);
        $(".course_bar").width(width * ratio);
    }
    
    
    $(document).ready(function(){
        $(".course_video")[0].play();
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

