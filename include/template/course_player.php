<?php if ($loginHandler->check_login()) { ?>
    <div class="course_return p-t-sm item_hover cursor"><div class="center"><i class="fa fa-chevron-up"></i><i class="fa fa-chevron-up"></i><i class="fa fa-chevron-up"></i></div></div>
    <div class="backdrop"></div>
    <div id="iframe_content" class="test_content backdrop_open" style="overflow:hidden;">
        <div class="course_bar primary">      
            <div class="course_title pull-left"><span class="course_title_span"></span><span class='p-v-sm zmdi zmdi-chevron-right fa-sm'></span><span class="course_test_title"></span></div>
            <div class="btn-group pull-right course_navigation m-l-sm">
                <a href="javascript:void(0)" value="quit" class="course_action course_go_for btn btn-default" title=<?php echo TranslationHandler::get_static_text("QUIT") ?>><i class="fa fa-sign-out"></i></a>
            </div>
            <div class="btn-group pull-right course_navigation">
                <a href="javascript:void(0)" value="go_backwards" class="course_action course_go_back btn btn-default" title=<?php echo TranslationHandler::get_static_text("PREVIOUS") ?>><i class="fa fa-chevron-left"></i></a>
                <a href="javascript:void(0)" value="go_forwards" class="course_action course_go_for btn btn-default" title=<?php echo TranslationHandler::get_static_text("NEXT") ?>><i class="fa fa-chevron-right"></i></a>
            </div>
            <div class="course_slide_counter pull-right"></div>
        </div>
        <iframe scrolling="no" id="scaled-frame" class="course_iframe" src=""></iframe>
    </div>
    <div id="hidden_element" style="display:none"></div>
<?php } ?>