<?php
require_once 'require.php';
require_once '../../include/handler/courseHandler.php';

$courseHandler = new CourseHandler();
?>
<script>current_thumbnail_id = undefined;</script>
<div class="row">
    <div class="col-md-12">
        
        <?php
        $type = isset($_GET["type"]) ? $_GET["type"] : null;
        $id = isset($_GET["id"]) ? $_GET["id"] : 0;
        switch($type) {
            case "course":
                if(!$courseHandler->get($id, "course")) {
                    ErrorHandler::show_error_page($courseHandler->error);
                    die();
                }
                ?>
                    <div class="widget">
                        <header class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("EDIT_COURSE"); ?></h4>
                        </header>
                        <hr class="widget-separator">

                        <div class="widget-body" style="padding-top:32px !important;">
                            <form method="post" action="" url="course.php?step=edit_course" id="edit_course" name="edit_course" enctype="multipart/form-data">
                                <input type="hidden" name="course_id" class="course_id" value="<?php echo $courseHandler->current_element->id; ?>"/>
                                <input type="hidden" name="color" class="pick_color" value="<?php echo $courseHandler->current_element->color; ?>"/>
                                <input type="hidden" name="thumbnail" class="thumbnail_picked" value="<?php echo $courseHandler->current_element->image_id; ?>" />
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
                                                    echo '<option value="'.$os["id"].'" '. ($courseHandler->current_element->os_id == $os["id"] ? 'selected' : '') .'>'.$os["title"].'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group m-b-sm">
                                            <label for="sort_order" class="control-label"><?php echo TranslationHandler::get_static_text("INSERT_AFTER"); ?>:</label>
                                            <select id="sort_order" name="sort_order" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <?php
                                                echo '<option value="0">'.TranslationHandler::get_static_text("BEGINNING").'</option>';
                                                $courseHandler->get_multiple(0, "course", $courseHandler->current_element->os_id);
                                                for($i = 0; $i < count($courseHandler->courses); $i++) {
                                                    if($courseHandler->courses[$i]->id == $courseHandler->current_element->id) {
                                                        continue;
                                                    }
                                                    
                                                    echo '<option value="'.$courseHandler->courses[$i]->sort_order.'" '. (array_key_exists(($i+1), $courseHandler->courses) && $courseHandler->courses[($i+1)]->id == $courseHandler->current_element->id ? 'selected' : '') .'>'.htmlspecialchars($courseHandler->courses[$i]->title).'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group m-b-sm">
                                            <label for="points"><?php echo TranslationHandler::get_static_text("POINT_AMOUNT"); ?></label>
                                            <input type="text" id="points" name="points" placeholder="0" value="<?php echo $courseHandler->current_element->points; ?>" class="form-control">
                                        </div>

                                        <div class="form-group m-b-sm">
                                            <label for="custom"><?php echo TranslationHandler::get_static_text("COLOR"); ?></label>
                                            <input type="text" id="custom" name="custom" value="<?php echo $courseHandler->current_element->color; ?>">
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
                                                                echo '<div class="avatar avatar-xl thumbnail_element" thumbnail_id="' . $value['id'] . '" style="cursor:pointer;z-index:10;'. ($courseHandler->current_element->image_id == $value['id'] ? '' : 'opacity: 0.5') .'"><div class="set_default_thumbnail '. (!$value["default_thumbnail"] ? 'hidden' : '') .'" '. ($value["default_thumbnail"] ? 'default_thumbnail="1"' : '') .' title="'.TranslationHandler::get_static_text("DEFAULT_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-home" style="display:initial !important;"></i></div><div class="delete_thumbnail hidden" title="'.TranslationHandler::get_static_text("DELETE_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-close" style="display:initial !important;"></i></div><img src="assets/images/thumbnails/' . htmlspecialchars($value['filename']) . '"/><div class="active_thumbnail '. ($courseHandler->current_element->image_id == $value['id'] ? '' : 'hidden') .'" title="'.TranslationHandler::get_static_text("PICK_THUMBNAIL").'" thumbnail_id="' . $value['id'] . '"><i class="zmdi zmdi-check" style="display:initial !important;"></i></div></div>';
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
                                                    echo '<option value="'.$language["id"].'" '. (SettingsHandler::get_settings()->language_id == $language["id"] ? 'selected' : '') .'>'.htmlspecialchars($language["title"]).'</option>';
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
                                        <div class="center no_translations_text hidden"><?php echo TranslationHandler::get_static_text("NO_COURSE_TRANSLATIONS"); ?></div>
                                        <div class="translations">
                                            <?php
                                            foreach(DbHandler::get_instance()->return_query("SELECT translation_course.*, language.title as language_title FROM translation_course INNER JOIN language ON language.id = translation_course.language_id WHERE translation_course.course_id = :course_id", $courseHandler->current_element->id) as $value) {
                                                echo ' <div class="translation_'. $value["language_id"] .' translation_element"><div class="user-card m-b-sm student_20" style="padding: 8px !important;background:#f0f0f1;"><div class="media"><div class="media-body"><input type="hidden" name="language_id[]" value="'. $value["language_id"] .'"/><div class="accordion translation_COURSE'. $value["language_id"] .'" id="accordion" role="tablist" aria-multiselectable="false"><div class=""><div class="panel-heading" role="tab" id="heading-COURSE'. $value["language_id"] .'"><a class="accordion-toggle collapsed" style="padding: 5px 0px 0px 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-COURSE'. $value["language_id"] .'" aria-expanded="false" aria-controls="collapse-COURSE'. $value["language_id"] .'"><label for="textarea'. $value["language_id"] .'" style="cursor:pointer">'. htmlspecialchars($value["language_title"]) .' '. TranslationHandler::get_static_text("TRANSLATION") .'</label><i class="fa acc-switch"></i><i class="zmdi zmdi-hc-lg zmdi-delete pull-right remove_translation" translation_id="'. $value["language_id"] .'" style="margin-top:1px;cursor:pointer;"></i></a></div><div id="collapse-COURSE'. $value["language_id"] .'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-COURSE'. $value["language_id"] .'" aria-expanded="false" style="height: 0px;"><div class="panel-body" style="padding: 5px 10px 10px 10px !important;"><label for="title" style="margin-bottom:0px !important;">'. TranslationHandler::get_static_text("TITLE") .'</label><input type="text" id="title" name="title[]" placeholder="" value="'.htmlspecialchars($value["title"]).'" class="form-control"><label for="description" style="margin: 10px 0px 0px 0px !important;">'. TranslationHandler::get_static_text("INFO_DESCRIPTION") .'</label><input type="text" id="description" name="description[]" placeholder="" value="'.htmlspecialchars($value["description"]).'" class="form-control"></div></div></div></div></div></div></div></div>';
                                            }
                                            ?>
                                        </div>

                                    </div>
                                </div>

                                <div style="clear:both"></div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="button" name="submit" id="update_course_button" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="pull-right btn btn-default submit_update_course">
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                <?php
                break;
            
            case "lecture":
                if(!$courseHandler->get($id, "lecture")) {
                    ErrorHandler::show_error_page($courseHandler->error);
                    die();
                }
                ?>
                    <div class="widget">
                        <header class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("EDIT_LECTURE"); ?></h4>
                        </header>
                        <hr class="widget-separator">

                        <div class="widget-body" style="padding-top:32px !important;">
                            <form method="post" action="" url="course.php?step=edit_lecture" id="edit_lecture" name="edit_lecture">
                                <input type="hidden" name="lecture_id" class="lecture_id" value="<?php echo $courseHandler->current_element->id; ?>"/>
                                <div class="title_text" style="display:none;"><?php echo TranslationHandler::get_static_text("TITLE"); ?></div>
                                <div class="description_text" style="display:none;"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></div>
                                <div class="translation_text" style="display:none;"><?php echo TranslationHandler::get_static_text("TRANSLATION"); ?></div>
                                <div class="translation_type" style="display:none;"><?php echo TranslationHandler::get_static_text("LECTURE"); ?></div>
                                
                                <div class="col-md-6">
                                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("LECTURE"); ?></h4>
                                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                                    <div class="col-md-12" style="margin-bottom: 16px !important;">
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="course_id" class="control-label"><?php echo TranslationHandler::get_static_text("ATTACH_TO_COURSE"); ?>:</label>
                                            <select id="course_id" name="course_id" class="form-control add_lectures" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <?php
                                                $courseHandler->get_multiple(0, "course");
                                                foreach($courseHandler->courses as $course) {
                                                    echo '<option value="'.$course->id.'" '. ($courseHandler->current_element->course_id == $course->id ? 'selected' : '') .'>'.htmlspecialchars($course->title).'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <?php
                                            $select_boxes = "";
                                            $courseHandler->get_multiple($courseHandler->current_element->course_id, "lecture");
                                            for($i = 0; $i < count($courseHandler->lectures); $i++) {
                                                if($courseHandler->lectures[$i]->id == $courseHandler->current_element->id) {
                                                    continue;
                                                }
                                                $select_boxes .= '<option value="'.$courseHandler->lectures[$i]->sort_order.'" '. (array_key_exists(($i+1), $courseHandler->lectures) && $courseHandler->lectures[($i+1)]->id == $courseHandler->current_element->id ? 'selected' : '') .'>'.htmlspecialchars($courseHandler->lectures[$i]->title).'</option>';
                                            }
                                        ?>
                                        
                                        <div class="form-group m-b-sm sort_order" style="<?php echo $select_boxes != "" ? "" : "opacity: 0;height:0px;margin-top:-10px !important;"; ?>">
                                            <label for="sort_order" class="control-label"><?php echo TranslationHandler::get_static_text("INSERT_AFTER"); ?>:</label>
                                            <select id="sort_order" name="sort_order" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <?php echo '<option value="0">'.TranslationHandler::get_static_text("BEGINNING").'</option>'; ?>
                                                <?php echo $select_boxes; ?>;
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="difficulty" class="control-label"><?php echo TranslationHandler::get_static_text("DIFFICULTY"); ?>:</label>
                                            <select id="difficulty" name="difficulty" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <option value="0" <?php echo !$courseHandler->current_element->advanced ? "selected" : ""; ?>><?php echo TranslationHandler::get_static_text("DIFFICULTY_BEGINNER"); ?></option>
                                                <option value="1" <?php echo $courseHandler->current_element->advanced ? "selected" : ""; ?>><?php echo TranslationHandler::get_static_text("DIFFICULTY_ADVANCED"); ?></option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="points"><?php echo TranslationHandler::get_static_text("POINT_AMOUNT"); ?></label>
                                            <input type="text" id="points" name="points" placeholder="0" value="<?php echo $courseHandler->current_element->points; ?>" class="form-control">
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
                                                    echo '<option value="'.$language["id"].'" '. (SettingsHandler::get_settings()->language_id == $language["id"] ? 'selected' : '') .'>'.htmlspecialchars($language["title"]).'</option>';
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
                                        
                                        <div class="center no_translations_text hidden"><?php echo TranslationHandler::get_static_text("NO_LECTURE_TRANSLATIONS"); ?></div>
                                        <div class="translations">
                                            <?php
                                            foreach(DbHandler::get_instance()->return_query("SELECT translation_course_lecture.*, language.title as language_title FROM translation_course_lecture INNER JOIN language ON language.id = translation_course_lecture.language_id WHERE translation_course_lecture.course_lecture_id = :lecture_id", $courseHandler->current_element->id) as $value) {
                                               echo ' <div class="translation_'. $value["language_id"] .' translation_element"><div class="user-card m-b-sm student_20" style="padding: 8px !important;background:#f0f0f1;"><div class="media"><div class="media-body"><input type="hidden" name="language_id[]" value="'. $value["language_id"] .'"/><div class="accordion translation_LECTURE'. $value["language_id"] .'" id="accordion" role="tablist" aria-multiselectable="false"><div class=""><div class="panel-heading" role="tab" id="heading-LECTURE'. $value["language_id"] .'"><a class="accordion-toggle collapsed" style="padding: 5px 0px 0px 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-LECTURE'. $value["language_id"] .'" aria-expanded="false" aria-controls="collapse-LECTURE'. $value["language_id"] .'"><label for="textarea'. $value["language_id"] .'" style="cursor:pointer">'. htmlspecialchars($value["language_title"]) .' '. TranslationHandler::get_static_text("TRANSLATION") .'</label><i class="fa acc-switch"></i><i class="zmdi zmdi-hc-lg zmdi-delete pull-right remove_translation" translation_id="'. $value["language_id"] .'" style="margin-top:1px;cursor:pointer;"></i></a></div><div id="collapse-LECTURE'. $value["language_id"] .'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-LECTURE'. $value["language_id"] .'" aria-expanded="false" style="height: 0px;"><div class="panel-body" style="padding: 5px 10px 10px 10px !important;"><label for="title" style="margin-bottom:0px !important;">'. TranslationHandler::get_static_text("TITLE") .'</label><input type="text" id="title" name="title[]" placeholder="" value="'.htmlspecialchars($value["title"]).'" class="form-control"><label for="description" style="margin: 10px 0px 0px 0px !important;">'. TranslationHandler::get_static_text("INFO_DESCRIPTION") .'</label><input type="text" id="description" name="description[]" placeholder="" value="'.(empty($value["description"]) ? '&nbsp;&nbsp;&nbsp;' : $value["description"]).'" class="form-control"></div></div></div></div></div></div></div></div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div style="clear:both"></div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="button" name="submit" id="update_course_button" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="pull-right btn btn-default submit_update_course">
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                <?php
                break;
            
            case "test":
                if(!$courseHandler->get($id, "test")) {
                    ErrorHandler::show_error_page($courseHandler->error);
                    die();
                }
                ?>
                    <div class="widget">
                        <header class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("EDIT_TEST"); ?></h4>
                        </header>
                        <hr class="widget-separator">

                        <div class="widget-body" style="padding-top:32px !important;">
                            <form method="post" action="" url="course.php?step=edit_test" id="edit_test" name="edit_test">
                                <input type="hidden" name="test_id" class="test_id" value="<?php echo $courseHandler->current_element->id; ?>"/>
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
                                                <?php
                                                $courseHandler->get_multiple(0, "course");
                                                foreach($courseHandler->courses as $course) {
                                                    echo '<option value="'.$course->id.'" '. ($courseHandler->current_element->course_id == $course->id ? 'selected' : '') .'>'.htmlspecialchars($course->title).'</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                        <?php
                                            $select_boxes = "";
                                            $courseHandler->get_multiple($courseHandler->current_element->course_id, "test");
                                            for($i = 0; $i < count($courseHandler->tests); $i++) {
                                                if($courseHandler->tests[$i]->id == $courseHandler->current_element->id) {
                                                    continue;
                                                }
                                                $select_boxes .= '<option value="'.$courseHandler->tests[$i]->sort_order.'" '. (array_key_exists(($i+1), $courseHandler->tests) && $courseHandler->tests[($i+1)]->id == $courseHandler->current_element->id ? 'selected' : '') .'>'.htmlspecialchars($courseHandler->tests[$i]->title).'</option>';
                                            }
                                        ?>
                                        
                                        <div class="form-group m-b-sm sort_order" style="<?php echo $select_boxes != "" ? "" : "opacity: 0;height:0px;margin-top:-10px !important;"; ?>">
                                            <label for="sort_order" class="control-label"><?php echo TranslationHandler::get_static_text("INSERT_AFTER"); ?>:</label>
                                            <select id="sort_order" name="sort_order" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <?php echo '<option value="0">'.TranslationHandler::get_static_text("BEGINNING").'</option>'; ?>
                                                <?php echo $select_boxes; ?>;
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="difficulty" class="control-label"><?php echo TranslationHandler::get_static_text("DIFFICULTY"); ?>:</label>
                                            <select id="difficulty" name="difficulty" class="form-control" data-options="{minimumResultsForSearch: Infinity}" data-plugin="select2">
                                                <option value="0" <?php echo !$courseHandler->current_element->advanced ? "selected" : ""; ?>><?php echo TranslationHandler::get_static_text("DIFFICULTY_BEGINNER"); ?></option>
                                                <option value="1" <?php echo $courseHandler->current_element->advanced ? "selected" : ""; ?>><?php echo TranslationHandler::get_static_text("DIFFICULTY_ADVANCED"); ?></option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group m-b-sm">
                                            <label for="points"><?php echo TranslationHandler::get_static_text("POINT_AMOUNT"); ?></label>
                                            <input type="text" id="points" name="points" placeholder="0" value="<?php echo $courseHandler->current_element->points; ?>" class="form-control">
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
                                                    echo '<option value="'.$language["id"].'" '. (SettingsHandler::get_settings()->language_id == $language["id"] ? 'selected' : '') .'>'.htmlspecialchars($language["title"]).'</option>';
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
                                        
                                        <div class="center no_translations_text hidden"><?php echo TranslationHandler::get_static_text("NO_TEST_TRANSLATIONS"); ?></div>
                                        <div class="translations">
                                            <?php
                                            foreach(DbHandler::get_instance()->return_query("SELECT translation_course_test.*, language.title as language_title FROM translation_course_test INNER JOIN language ON language.id = translation_course_test.language_id WHERE translation_course_test.course_test_id = :test_id", $courseHandler->current_element->id) as $value) {
                                               echo ' <div class="translation_'. $value["language_id"] .' translation_element"><div class="user-card m-b-sm student_20" style="padding: 8px !important;background:#f0f0f1;"><div class="media"><div class="media-body"><input type="hidden" name="language_id[]" value="'. $value["language_id"] .'"/><div class="accordion translation_TEST'. $value["language_id"] .'" id="accordion" role="tablist" aria-multiselectable="false"><div class=""><div class="panel-heading" role="tab" id="heading-TEST'. $value["language_id"] .'"><a class="accordion-toggle collapsed" style="padding: 5px 0px 0px 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-TEST'. $value["language_id"] .'" aria-expanded="false" aria-controls="collapse-TEST'. $value["language_id"] .'"><label for="textarea'. $value["language_id"] .'" style="cursor:pointer">'. htmlspecialchars($value["language_title"]) .' '. TranslationHandler::get_static_text("TRANSLATION") .'</label><i class="fa acc-switch"></i><i class="zmdi zmdi-hc-lg zmdi-delete pull-right remove_translation" translation_id="'. $value["language_id"] .'" style="margin-top:1px;cursor:pointer;"></i></a></div><div id="collapse-TEST'. $value["language_id"] .'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-TEST'. $value["language_id"] .'" aria-expanded="false" style="height: 0px;"><div class="panel-body" style="padding: 5px 10px 10px 10px !important;"><label for="title" style="margin-bottom:0px !important;">'. TranslationHandler::get_static_text("TITLE") .'</label><input type="text" id="title" name="title[]" placeholder="" value="'.htmlspecialchars($value["title"]).'" class="form-control"><label for="description" style="margin: 10px 0px 0px 0px !important;">'. TranslationHandler::get_static_text("INFO_DESCRIPTION") .'</label><input type="text" id="description" name="description[]" placeholder="" value="'.(empty($value["description"]) ? '&nbsp;&nbsp;&nbsp;' : $value["description"]).'" class="form-control"></div></div></div></div></div></div></div></div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div style="clear:both"></div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="button" name="submit" id="update_course_button" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="pull-right btn btn-default submit_update_course">
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                <?php
                break;
            
            
            default:
                ErrorHandler::show_error_page();
                die();
                
            error:
                ErrorHandler::show_error_page();
                die();
        }
        ?>
        
                    
    </div>
</div>

<script src="assets/js/include_app.js" type="text/javascript"></script>
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
    });
</script>