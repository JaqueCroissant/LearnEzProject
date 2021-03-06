<?php
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';

$courseHandler = new CourseHandler();
?>
<script>current_thumbnail_id = undefined;</script>
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
                            <form method="post" action="" url="course.php?step=create_course" id="create_course" name="create_course" enctype="multipart/form-data">
                                <input type="hidden" name="color" class="pick_color" value="#000000"/>
                                <input type="hidden" name="thumbnail" class="thumbnail_picked" value="" />
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
                                            <select id="os" name="os" class="form-control add_courses" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <option disabled selected></option>
                                                <?php
                                                foreach(CourseHandler::get_os_options() as $os) {
                                                    echo '<option value="'.$os["id"].'">'.  htmlspecialchars($os["title"]).'</option>';
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
                                            <label for="points"><?php echo TranslationHandler::get_static_text("POINT_AMOUNT"); ?></label>
                                            <input type="text" id="points" name="points" placeholder="0" class="form-control">
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="custom"><?php echo TranslationHandler::get_static_text("COLOR"); ?></label>
                                            <input type="text" id="custom" name="custom">
                                        </div>
                                        
                                    </div>
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("THUMBNAIL"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        
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
                                                        <div class="panel-body thumbnail-placeholder" style="padding-left:0px !important;padding-right:0px !important;">
                                                            <?php
                                                            foreach ($courseHandler->get_thumbnails() as $value) {
                                                                echo '<div class="avatar avatar-xl thumbnail_element" thumbnail_id="' . $value['id'] . '" style="cursor:pointer;z-index:10"><div class="set_default_thumbnail '. (!$value["default_thumbnail"] ? 'hidden' : '') .'" '. ($value["default_thumbnail"] ? 'default_thumbnail="1"' : '') .' title="'.TranslationHandler::get_static_text("DEFAULT_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-home" style="display:initial !important;"></i></div><div class="delete_thumbnail delete_thumbnail_style hidden" title="'.TranslationHandler::get_static_text("DELETE_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-close" style="display:initial !important;"></i></div><img src="assets/images/thumbnails/' . htmlspecialchars($value['filename']) . '"/><div class="active_thumbnail hidden" title="'.TranslationHandler::get_static_text("PICK_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-check" style="display:initial !important;"></i></div></div>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
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
                                                        <div style="display: table-cell;width: 100%;">
                                                        <input type="file" id="thumbnail_upload" name="thumbnail_upload" class="form-control btn btn-default">
                                                        </div>
                                                        <div style="display: table-cell;vertical-align: bottom;white-space: nowrap;">
                                                        <input type="button" name="submit" id="upload_thumbnail" value="<?php echo TranslationHandler::get_static_text("UPLOAD_IMAGE"); ?>" class="pull-right btn btn-default btn-md upload_thumbnail m-l-sm">
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
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
                                                    echo '<option value="'.$language["id"].'" '. (SettingsHandler::get_settings()->language_id == $language["id"] ? 'selected' : '') .'>'.  htmlspecialchars($language["title"]).'</option>';
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
                            <form method="post" action="" url="course.php?step=create_lecture" id="create_lecture" name="create_lecture" enctype="multipart/form-data">
                                <input type="hidden" id="lecture_file_name" name="file_name" value="" />
                                <input type="hidden" id="lecture_total_length" name="total_length" value="" />
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
                                                $courseHandler->get_multiple(0, "course");
                                                foreach($courseHandler->courses as $course) {
                                                    echo '<option value="'.$course->id.'">'.  htmlspecialchars($course->title).'</option>';
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
                                    
                                   
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("MEDIA"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        
                                        <div class="form-group m-b-sm">
                                            <div class="form-group m-b-sm">
                                                <div style="display: table-cell;width: 100%;">
                                                <input type="file" id="thumbnail_lecture" name="thumbnail_lecture" class="form-control btn btn-default">
                                                </div>
                                                <div style="display: table-cell;vertical-align: bottom;white-space: nowrap;">
                                                <input type="button" name="submit" id="upload_lecture" value="<?php echo TranslationHandler::get_static_text("UPLOAD"); ?>" class="pull-right btn btn-default btn-md upload_lecture m-l-sm">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="lecture_progress" style="opacity:0">
                                            
                                            <div style="display: table-cell;width: 100%;">
                                                <div class="progress progress_progress progress-lg pull-left" style="height:38px !important;width:100%;border-radius: 3px; box-shadow: none;border:1px solid #ddd;">
                                                    <div class="progress-bar lecture_progress_bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="lecture_progress_value"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="display: table-cell;vertical-align: top;white-space: nowrap;">
                                                <input type="button" name="submit" id="cancel_lecture_upload" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>" class="pull-right btn btn-default btn-md cancel_lecture_upload m-l-sm">
                                            </div>
                                        </div>
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
                                                    echo '<option value="'.$language["id"].'" '. (SettingsHandler::get_settings()->language_id == $language["id"] ? 'selected' : '') .'>'.  htmlspecialchars($language["title"]).'</option>';
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
                            <form method="post" action="" url="course.php?step=create_test" id="create_test" name="create_test" enctype="multipart/form-data">
                                <input type="hidden" id="test_file_name" name="file_name" value="" />
                                <input type="hidden" id="test_total_steps" name="total_steps" value="" />
                                
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
                                                $courseHandler->get_multiple(0, "course");
                                                foreach($courseHandler->courses as $course) {
                                                    echo '<option value="'.$course->id.'">'.  htmlspecialchars($course->title).'</option>';
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
                                    
                                    
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("MEDIA"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        
                                        <div class="form-group m-b-sm">
                                            <div class="form-group m-b-sm">
                                                <div style="display: table-cell;width: 100%;">
                                                <input type="file" id="thumbnail_test" name="thumbnail_test" class="form-control btn btn-default">
                                                </div>
                                                <div style="display: table-cell;vertical-align: bottom;white-space: nowrap;">
                                                <input type="button" name="submit" id="upload_test" value="<?php echo TranslationHandler::get_static_text("UPLOAD"); ?>" class="pull-right btn btn-default btn-md upload_test m-l-sm">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="test_progress" style="opacity:0">
                                            
                                            <div style="display: table-cell;width: 100%;">
                                                <div class="progress progress_progress progress-lg pull-left" style="height:38px !important;width:100%;border-radius: 3px; box-shadow: none;border:1px solid #ddd;">
                                                    <div class="progress-bar test_progress_bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="test_progress_value"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="display: table-cell;vertical-align: top;white-space: nowrap;">
                                                <input type="button" name="submit" id="cancel_test_upload" value="<?php echo TranslationHandler::get_static_text("CANCEL"); ?>" class="pull-right btn btn-default btn-md cancel_test_upload m-l-sm">
                                            </div>
                                        </div>
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
                                                    echo '<option value="'.$language["id"].'" '. (SettingsHandler::get_settings()->language_id == $language["id"] ? 'selected' : '') .'>'.  htmlspecialchars($language["title"]).'</option>';
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

<iframe src="" id="test_player" style="display:none"></iframe>

<script src="assets/js/include_app.js" type="text/javascript"></script>
<script src="js/my_tab.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $("#custom").spectrum({
            preferredFormat: "hex",
            showInput: true,
            hideAfterPaletteSelect:true,
            replacerClassName: 'form-control',
            change: function(color) {
                $(".pick_color").val(color.toHexString());
            }
        });
        
        var max_width = $(".cancel_test_upload").width() >= $(".upload_test").width() ? $(".cancel_test_upload").width() : $(".upload_test").width();
        var max_width_lecture = $(".cancel_lecture_upload").width() >= $(".upload_lecture").width() ? $(".cancel_lecture_upload").width() : $(".upload_lecture").width();
        
        $(".cancel_test_upload").width(max_width);
        $(".upload_test").width(max_width);
        $(".cancel_lecture_upload").width(max_width_lecture);
        $(".upload_lecture").width(max_width_lecture);
        
    });
</script>