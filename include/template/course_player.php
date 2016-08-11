<?php if ($loginHandler->check_login()) { ?>
<div class="course_return item_hover cursor"><div class="center p-l-sm p-r-sm"><i class="fa fa-chevron-up m-r-sm"></i><span class="course_player_test_title"></span><i class="zmdi zmdi-graduation-cap m-l-sm"></i></div></div>
    <div class="backdrop"></div>
    <div id="iframe_content" class="test_content backdrop_open" style="overflow:hidden;">
        <div class="course_bar primary course_window_test">      
            <div class="course_title pull-left"><span class="course_player_course_title"></span><span class='p-v-sm zmdi zmdi-chevron-right fa-sm'></span><span class="course_player_test_title"></span></div>
            <div class="btn-group pull-right course_navigation">
                <a href="javascript:void(0)" value="minimize" class="course_action course_default course_minimize btn btn-default" data-toggle="tooltip" data-container="#iframe_content" data-trigger="hover" data-placement="bottom" title="<?php echo TranslationHandler::get_static_text("MINIMIZE") ?>"><i class="zmdi zmdi-window-minimize"></i></a>
                <a href="javascript:void(0)" value="quit" class="course_action course_default course_quit btn btn-default" data-toggle="tooltip" data-container="#iframe_content" data-trigger="hover" data-placement="bottom" title=<?php echo TranslationHandler::get_static_text("QUIT") ?>><i class="zmdi zmdi-close"></i></a>
            </div>
            <div class="course_test_menu" style="display:none;">
                <div class="pull-right course_navigation">
                    <a href="javascript:void(0)" value="mute" class="course_action course_mute btn btn-default" data-toggle="tooltip" data-container="#iframe_content" data-trigger="hover" data-placement="bottom" title="<?php echo TranslationHandler::get_static_text("MUTE") ?>"><i class="zmdi zmdi-volume-up"></i></a>
                    <a style="display:none;" href="javascript:void(0)" value="mute" class="course_action course_unmute btn btn-default" data-toggle="tooltip" data-container="#iframe_content" data-trigger="hover" data-placement="bottom" title="<?php echo TranslationHandler::get_static_text("UNMUTE") ?>"><i class="zmdi zmdi-volume-off"></i></a>
                </div>
                <div class="btn-group pull-right course_navigation">
                    <a href="javascript:void(0)" value="go_backwards" class="course_action course_go_back btn btn-default" data-toggle="tooltip" data-container="#iframe_content" data-trigger="hover" data-placement="bottom" title="<?php echo TranslationHandler::get_static_text("PREVIOUS") ?>"><i class="fa fa-chevron-left"></i></a>
                    <a href="javascript:void(0)" value="go_forwards" class="course_action course_go_for btn btn-default" data-toggle="tooltip" data-container="#iframe_content" data-trigger="hover" data-placement="bottom" title="<?php echo TranslationHandler::get_static_text("NEXT") ?>"><i class="fa fa-chevron-right"></i></a>
                    
                </div>
            </div>
            <div class="course_lecture_menu" style="display:none;">
                <div class="pull-right course_navigation">
                    <a href="javascript:void(0)" value="pause" class="course_action course_lecture_button course_pause btn btn-default" data-toggle="tooltip" data-container="#iframe_content" data-trigger="hover" data-placement="bottom" title="<?php echo TranslationHandler::get_static_text("PAUSE") ?>"><i class="zmdi zmdi-pause"></i></a>
                    <a style="display:none;" href="javascript:void(0)" value="play" class="course_action course_lecture_button course_play btn btn-default" data-toggle="tooltip" data-container="#iframe_content" data-trigger="hover" data-placement="bottom" title="<?php echo TranslationHandler::get_static_text("PLAY") ?>"><i class="zmdi zmdi-play"></i></a>
                </div>
                <div class="btn-group pull-right course_navigation">
                    <a href="javascript:void(0)" value="repeat" class="course_action course_lecture_button course_repeat btn btn-default" data-toggle="tooltip" data-container="#iframe_content" data-trigger="hover" data-placement="bottom" title="<?php echo TranslationHandler::get_static_text("REPEAT") ?>"><i class="zmdi zmdi-repeat"></i></a>
                    <a href="javascript:void(0)" value="continue" class="course_action course_continue btn btn-default" data-toggle="tooltip" data-container="#iframe_content" data-trigger="hover" data-placement="bottom" title="<?php echo TranslationHandler::get_static_text("CONTINUE") ?>"><i class="zmdi zmdi-skip-next"></i></a>
                </div>
            </div>
            <div class="course_slide_counter pull-right"></div>
        </div>
        <iframe scrolling="no" id="scaled-frame" class="course_iframe course_window_test" src="" style="display:none;"></iframe>
        <video class="course_video" src="" style="display:none;"></video>
    </div>
    <div id="hidden_element" style="display:none"></div>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip(); 
        })
    </script>
<?php } ?>