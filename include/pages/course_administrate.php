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
                    if (RightsHandler::has_page_right("COURSE_ADMINISTRATE")) {
                    ?>
                    <li role="presentation" id="create_course_header"><a href="#create_course_tab" class="my_tab_header" id="create_course_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("CREATE_COURSE"); ?></a></li>
                    <?php
                    }
                    
                    if (RightsHandler::has_page_right("COURSE_ADMINISTRATE")) {
                    ?>
                        <li role="presentation" id="create_lecture_header"><a href="#create_lecture_tab" class="my_tab_header" id="create_lecture_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("CREATE_LECTURE"); ?></a></li>
                    <?php
                    }
                    
                    if (RightsHandler::has_page_right("COURSE_ADMINISTRATE")) {
                    ?>
                        <li role="presentation" id="create_test_header"><a href="#create_test_tab" class="my_tab_header" id="create_test_a" data-toggle="tab"><?php echo TranslationHandler::get_static_text("CREATE_TEST"); ?></a></li>
                    <?php
                    }
                    ?>
                </ul>
                
                
                <div class="my_tab_content" >
                    <?php
                    if (RightsHandler::has_page_right("COURSE_ADMINISTRATE")) {
                    ?>
                    <div class="my_fade my_tab" id="create_course_tab">
                        <div class="widget-body" style="padding-top:32px !important;">
                            <form method="post" action="" url="course.php?step=create_course" id="create_course" name="create_course">
                                <div class="title_text" style="display:none;"><?php echo TranslationHandler::get_static_text("TITLE"); ?></div>
                                <div class="description_text" style="display:none;"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></div>
                                <div class="translation_text" style="display:none;"><?php echo TranslationHandler::get_static_text("TRANSLATION"); ?></div>
                                <div class="translation_type" style="display:none;">COURSE</div>
                                
                                
                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("COURSE"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        <div class="form-group m-b-sm">
                                            <label for="os" class="control-label"><?php echo TranslationHandler::get_static_text("OS"); ?>:</label>
                                            <select id="os" name="os" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <?php
                                                foreach(CourseHandler::get_os_options() as $os) {
                                                    echo '<option value="'.$os["id"].'">'.$os["title"].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="sort_order" class="control-label"><?php echo TranslationHandler::get_static_text("INSERT_AFTER"); ?>:</label>
                                            <select id="sort_order" name="sort_order" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <?php
                                                foreach($courseHandler->get_all_courses() as $course) {
                                                    echo '<option value="'.$course->sort_order.'">'.$course->title.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="points"><?php echo TranslationHandler::get_static_text("POINT_AMOUNT"); ?></label>
                                            <input type="text" id="points" name="points" placeholder="0" class="form-control">
                                        </div>
                                        
                                    </div>
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("THUMBNAIL"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        
                                        <div class="form-group m-b-sm">
                                            <div class="panel-group accordion" id="accordion-new-thumbnail-course" role="tablist" aria-multiselectable="false">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="heading-new-thumbnail-course">
                                                        <a class="accordion-toggle" style="padding: 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion-new-thumbnail-course" href="#collapse-new-thumbnail-course" aria-expanded="false" aria-controls="collapse-new-thumbnail-course">
                                                            <label for="textarea1" style="cursor:pointer;"><?php echo TranslationHandler::get_static_text("UPLOAD_NEW_THUMBNAIL"); ?></label>
                                                            <i class="fa acc-switch"></i>
                                                        </a>
                                                    </div>
                                                    <div id="collapse-new-thumbnail-course" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-new-thumbnail-course" aria-expanded="false">
                                                    <hr class="m-0 " style="border-color: #ddd;margin: 2px 0px 14px 0px !important;">
                                                    <div class="form-group m-b-sm">
                                                        <input type="file" id="thumbnail_upload" name="thumbnail_upload" class="form-control btn btn-default">
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <div class="form-group m-b-sm">
                                            <div class="panel-group accordion" id="accordion-thumbnail-course" role="tablist" aria-multiselectable="false">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="heading-thumbnail-course">
                                                        <a class="accordion-toggle" style="padding: 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion-thumbnail-course" href="#collapse-thumbnail-course" aria-expanded="false" aria-controls="collapse-thumbnail-course">
                                                            <label for="textarea1" style="cursor:pointer;"><?php echo TranslationHandler::get_static_text("PICK_FROM_EXISTING_THUMBNAIL"); ?></label>
                                                            <i class="fa acc-switch"></i>
                                                        </a>
                                                    </div>
                                                    <div id="collapse-thumbnail-course" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-thumbnail-course" aria-expanded="false">
                                                        <div class="panel-body" style="padding-left:0px !important;padding-right:0px !important;">
                                                            <?php
                                                            foreach ($courseHandler->get_thumbnails() as $value) {
                                                                echo '<div class="avatar avatar-xl avatar-circle avatar-hover" thumbnail_id="' . $value['id'] . '"><img src="assets/images/thumbnails/' . $value['filename'] . '"/></div>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("MEDIA"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("COURSE_INFORMATION"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12">
                                        
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
                                            <input type="button" name="submit" id="add_translation" value="<?php echo TranslationHandler::get_static_text("ADD"); ?>" class="pull-right btn btn-default btn-md add_translation m-l-sm">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group m-b-sm blocked_students">
                                            <hr class="m-0 " style="border-color: #ddd;margin: 2px 0px 14px 0px !important;">
                                        </div>
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
                    if (RightsHandler::has_page_right("COURSE_ADMINISTRATE")) {
                    ?>
                    <div class="my_fade my_tab" id="create_lecture_tab">
                        <div class="widget-body" style="padding-top:32px !important;">
                            <form method="post" action="" url="course.php?step=create_lecture" id="create_lecture" name="create_lecture">
                                <div class="title_text" style="display:none;"><?php echo TranslationHandler::get_static_text("TITLE"); ?></div>
                                <div class="description_text" style="display:none;"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></div>
                                <div class="translation_text" style="display:none;"><?php echo TranslationHandler::get_static_text("TRANSLATION"); ?></div>
                                <div class="translation_type" style="display:none;">LECTURE</div>
                                
                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("LECTURE"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="course_id" class="control-label"><?php echo TranslationHandler::get_static_text("ATTACH_TO_COURSE"); ?>:</label>
                                            <select id="course_id" name="course_id" class="form-control add_lectures" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <option disabled selected></option>
                                                <?php
                                                foreach($courseHandler->get_all_courses() as $course) {
                                                    echo '<option value="'.$course->id.'">'.$course->title.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm sort_order" style="opacity: 0;height:0px;margin-top:-10px !important;">
                                            <label for="sort_order" class="control-label"><?php echo TranslationHandler::get_static_text("INSERT_AFTER"); ?>:</label>
                                            <select id="sort_order" name="sort_order" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="difficulty" class="control-label"><?php echo TranslationHandler::get_static_text("DIFFICULTY"); ?>:</label>
                                            <select id="difficulty" name="difficulty" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <option value="0"><?php echo TranslationHandler::get_static_text("DIFFICULTY_BEGINNER"); ?></option>
                                                <option value="1"><?php echo TranslationHandler::get_static_text("DIFFICULTY_ADVANCED"); ?></option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="points"><?php echo TranslationHandler::get_static_text("POINT_AMOUNT"); ?></label>
                                            <input type="text" id="points" name="points" placeholder="0" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("THUMBNAIL"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        
                                        <div class="form-group m-b-sm">
                                            <div class="panel-group accordion" id="accordion-new-thumbnail-lecture" role="tablist" aria-multiselectable="false">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="heading-new-thumbnail-lecture">
                                                        <a class="accordion-toggle" style="padding: 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion-new-thumbnail-lecture" href="#collapse-new-thumbnail-lecture" aria-expanded="false" aria-controls="collapse-new-thumbnail-lecture">
                                                            <label for="textarea1" style="cursor:pointer;"><?php echo TranslationHandler::get_static_text("UPLOAD_NEW_THUMBNAIL"); ?></label>
                                                            <i class="fa acc-switch"></i>
                                                        </a>
                                                    </div>
                                                    <div id="collapse-new-thumbnail-lecture" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-new-thumbnail-lecture" aria-expanded="false">
                                                    <hr class="m-0 " style="border-color: #ddd;margin: 2px 0px 14px 0px !important;">
                                                    <div class="form-group m-b-sm">
                                                        <input type="file" id="thumbnail_upload" name="thumbnail_upload" class="form-control btn btn-default">
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <div class="form-group m-b-sm">
                                            <div class="panel-group accordion" id="accordion-thumbnail-lecture" role="tablist" aria-multiselectable="false">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="heading-thumbnail-lecture">
                                                        <a class="accordion-toggle" style="padding: 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion-thumbnail-lecture" href="#collapse-thumbnail-lecture" aria-expanded="false" aria-controls="collapse-thumbnail-lecture">
                                                            <label for="textarea1" style="cursor:pointer;"><?php echo TranslationHandler::get_static_text("PICK_FROM_EXISTING_THUMBNAIL"); ?></label>
                                                            <i class="fa acc-switch"></i>
                                                        </a>
                                                    </div>
                                                    <div id="collapse-thumbnail-lecture" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-thumbnail-lecture" aria-expanded="false">
                                                        <div class="panel-body" style="padding-left:0px !important;padding-right:0px !important;">
                                                            <?php
                                                            foreach ($courseHandler->get_thumbnails() as $value) {
                                                                echo '<div class="avatar avatar-xl avatar-circle avatar-hover" thumbnail_id="' . $value['id'] . '"><img src="assets/images/thumbnails/' . $value['filename'] . '"/></div>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("MEDIA"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("LECTURE_INFORMATION"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12">
                                        
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
                                            <input type="button" name="submit" id="add_translation" value="<?php echo TranslationHandler::get_static_text("ADD"); ?>" class="pull-right btn btn-default btn-md add_translation m-l-sm">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group m-b-sm blocked_students">
                                            <hr class="m-0 " style="border-color: #ddd;margin: 2px 0px 14px 0px !important;">
                                        </div>
                                        
                                        <div class="center no_translations_text"><?php echo TranslationHandler::get_static_text("NO_LECTURE_TRANSLATIONS"); ?></div>
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
                    if (RightsHandler::has_page_right("COURSE_ADMINISTRATE")) {
                    ?>
                    <div class="my_fade my_tab" id="create_test_tab">
                        <div class="widget-body" style="padding-top:32px !important;">
                            <form method="post" action="" url="course.php?step=create_test" id="create_test" name="create_test">
                                <div class="title_text" style="display:none;"><?php echo TranslationHandler::get_static_text("TITLE"); ?></div>
                                <div class="description_text" style="display:none;"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></div>
                                <div class="translation_text" style="display:none;"><?php echo TranslationHandler::get_static_text("TRANSLATION"); ?></div>
                                <div class="translation_type" style="display:none;">TEST</div>
                                
                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("TEST"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="course_id" class="control-label"><?php echo TranslationHandler::get_static_text("ATTACH_TO_COURSE"); ?>:</label>
                                            <select id="course_id" name="course_id" class="form-control add_tests" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <option disabled selected></option>
                                                <?php
                                                foreach($courseHandler->get_all_courses() as $course) {
                                                    echo '<option value="'.$course->id.'">'.$course->title.'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm sort_order" style="opacity: 0;height:0px;margin-top:-10px !important;">
                                            <label for="sort_order" class="control-label"><?php echo TranslationHandler::get_static_text("INSERT_AFTER"); ?>:</label>
                                            <select id="sort_order" name="sort_order" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="difficulty" class="control-label"><?php echo TranslationHandler::get_static_text("DIFFICULTY"); ?>:</label>
                                            <select id="difficulty" name="difficulty" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <option value="0"><?php echo TranslationHandler::get_static_text("DIFFICULTY_BEGINNER"); ?></option>
                                                <option value="1"><?php echo TranslationHandler::get_static_text("DIFFICULTY_ADVANCED"); ?></option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="points"><?php echo TranslationHandler::get_static_text("POINT_AMOUNT"); ?></label>
                                            <input type="text" id="points" name="points" placeholder="0" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("THUMBNAIL"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        
                                        <div class="form-group m-b-sm">
                                            <div class="panel-group accordion" id="accordion-new-thumbnail-test" role="tablist" aria-multiselectable="false">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="heading-new-thumbnail-test">
                                                        <a class="accordion-toggle" style="padding: 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion-new-thumbnail-test" href="#collapse-new-thumbnail-test" aria-expanded="false" aria-controls="collapse-new-thumbnail-test">
                                                            <label for="textarea1" style="cursor:pointer;"><?php echo TranslationHandler::get_static_text("UPLOAD_NEW_THUMBNAIL"); ?></label>
                                                            <i class="fa acc-switch"></i>
                                                        </a>
                                                    </div>
                                                    <div id="collapse-new-thumbnail-test" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-new-thumbnail-test" aria-expanded="false">
                                                    <hr class="m-0 " style="border-color: #ddd;margin: 2px 0px 14px 0px !important;">
                                                    <div class="form-group m-b-sm">
                                                        <input type="file" id="thumbnail_upload" name="thumbnail_upload" class="form-control btn btn-default">
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <div class="form-group m-b-sm">
                                            <div class="panel-group accordion" id="accordion-thumbnail-test" role="tablist" aria-multiselectable="false">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="heading-thumbnail-test">
                                                        <a class="accordion-toggle" style="padding: 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion-thumbnail-test" href="#collapse-thumbnail-test" aria-expanded="false" aria-controls="collapse-thumbnail-test">
                                                            <label for="textarea1" style="cursor:pointer;"><?php echo TranslationHandler::get_static_text("PICK_FROM_EXISTING_THUMBNAIL"); ?></label>
                                                            <i class="fa acc-switch"></i>
                                                        </a>
                                                    </div>
                                                    <div id="collapse-thumbnail-test" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-thumbnail-test" aria-expanded="false">
                                                        <div class="panel-body" style="padding-left:0px !important;padding-right:0px !important;">
                                                            <?php
                                                            foreach ($courseHandler->get_thumbnails() as $value) {
                                                                echo '<div class="avatar avatar-xl avatar-circle avatar-hover" thumbnail_id="' . $value['id'] . '"><img src="assets/images/thumbnails/' . $value['filename'] . '"/></div>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("MEDIA"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("TEST_INFORMATION"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12">
                                        
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
                                            <input type="button" name="submit" id="add_translation" value="<?php echo TranslationHandler::get_static_text("ADD"); ?>" class="pull-right btn btn-default btn-md add_translation m-l-sm">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group m-b-sm blocked_students">
                                            <hr class="m-0 " style="border-color: #ddd;margin: 2px 0px 14px 0px !important;">
                                        </div>
                                        
                                        <div class="center no_translations_text"><?php echo TranslationHandler::get_static_text("NO_TEST_TRANSLATIONS"); ?></div>
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
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>