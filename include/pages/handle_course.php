<?php
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';

$courseHandler = new CourseHandler();
?>

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="m-b-lg nav-tabs-horizontal">
                <ul class="nav nav-tabs" role="tablist">
                    <?php
                    if (RightsHandler::has_page_right("HANDLE_COURSE_CREATE_COURSE")) {
                    ?>
                    <li role="presentation" id="create_course_header"><a href="#create_course_tab" class="my_tab_header" id="create_course_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("CREATE_COURSE"); ?></a></li>
                    <?php
                    }
                    
                    if (RightsHandler::has_page_right("HANDLE_COURSE_CREATE_LECTURE")) {
                    ?>
                        <li role="presentation" id="create_lecture_header"><a href="#create_lecture_tab" class="my_tab_header" id="create_lecture_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("CREATE_LECTURE"); ?></a></li>
                    <?php
                    }
                    
                    if (RightsHandler::has_page_right("HANDLE_COURSE_CREATE_TEST")) {
                    ?>
                        <li role="presentation" id="create_test_header"><a href="#create_test_tab" class="my_tab_header" id="create_test_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("CREATE_TEST"); ?></a></li>
                    <?php
                    }
                    ?>
                </ul>
                
                
                <div class="my_tab_content" >
                    <?php
                    if (RightsHandler::has_page_right("HANDLE_COURSE_CREATE_COURSE")) {
                    ?>
                    <div class="my_fade my_tab" id="create_course_tab">
                        <div class="widget-body" style="padding-top:32px !important;">
                            <form method="post" action="" url="course.php?step=create_course" id="create_course" name="create_course">
                                <div class="title_text" style="display:none;"><?php echo TranslationHandler::get_static_text("TITLE"); ?></div>
                                <div class="description_text" style="display:none;"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></div>
                                <div class="translation_text" style="display:none;"><?php echo TranslationHandler::get_static_text("TRANSLATION"); ?></div>
                                
                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("COURSE"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        <div class="form-group m-b-sm">
                                            <div style="display: table-cell;width: 100%;">
                                            <label for="language" class="control-label"><?php echo TranslationHandler::get_static_text("PICK_LANGUAGE"); ?>:</label>
                                            <select id="language" name="language" class="form-control pull-left" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <?php
                                                foreach(TranslationHandler::get_language_options() as $language) {
                                                    echo '<option value="'.$language["id"].'" '. (SettingsHandler::get_settings()->language_id == $language["id"] ? 'selected' : '') .'>'.$language["title"].'</option>';
                                                }
                                                ?>
                                            </select>
                                            </div>
                                            <div style="display: table-cell;vertical-align: bottom;white-space: nowrap;">
                                            <input type="button" name="submit" id="add_translation" value="<?php echo TranslationHandler::get_static_text("ADD"); ?>" class="pull-right btn btn-default btn-md add_course_translation m-l-sm">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="os" class="control-label"><?php echo TranslationHandler::get_static_text("OS"); ?>:</label>
                                            <select id="os" name="os" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <?php
                                                $course_os_data = DbHandler::get_instance()->return_query("SELECT course_os.id, translation_course_os.title FROM course_os INNER JOIN translation_course_os ON translation_course_os.course_os_id = course_os.id WHERE translation_course_os.language_id = :language_id", TranslationHandler::get_current_language());
                                                foreach($course_os_data as $os) {
                                                    echo '<option value="'.$os["id"].'">'.$os["title"].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="points"><?php echo TranslationHandler::get_static_text("POINT_AMOUNT"); ?></label>
                                            <input type="text" id="points" name="points" placeholder="0" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("COURSE_INFORMATION"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12">
                                        <div class="center no_translations_text"><?php echo TranslationHandler::get_static_text("NO_COURSE_TRANSLATIONS"); ?></div>
                                        <div class="translations">
                                        </div>
                                        
                                    </div>
                                </div>

                                <div style="clear:both"></div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="button" name="submit" id="create_course_button" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="pull-right btn btn-default submit_create_course">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php 
                    }
                    ?>
                    
                    <?php
                    if (RightsHandler::has_page_right("HANDLE_COURSE_CREATE_LECTURE")) {
                    ?>
                    <div class="my_fade my_tab" id="create_lecture_tab">
                        <div class="widget-body">
                            lecture
                        </div>
                    </div>
                    <?php 
                    }
                    ?>
                    
                    <?php
                    if (RightsHandler::has_page_right("HANDLE_COURSE_CREATE_TEST")) {
                    ?>
                    <div class="my_fade my_tab" id="create_test_tab">
                        <div class="widget-body">
                            test
                        </div>
                    </div>
                    <?php 
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>