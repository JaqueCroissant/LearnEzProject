<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/courseHandler.php';
$classHandler = new ClassHandler();
$schoolHandler = new SchoolHandler();
$courseHandler = new CourseHandler();
$schoolHandler->get_all_schools();
$schoolHandler->get_school_types();
$all_courses = [];
$school_courses = [];
if (RightsHandler::has_user_right("SCHOOL_EDIT") && ($classHandler->_user->user_type_id == 1 || $classHandler->_user->school_id == $_GET['school_id'])) {
    if (isset($_GET['school_id'])) {
        $schoolHandler->get_school_by_id($_GET['school_id']);
        $all_courses = $courseHandler->get_multiple(0, "course") ? $courseHandler->courses : [];
        $school_courses = $courseHandler->get_for_school($_GET['school_id'], 0, "course") ? $courseHandler->courses : [];
    }
    ?>

    <div class="row">   
        <div class="col-md-12">
            <div class="widget">
                <div class="widget-header">
                    <h4 class="widget-title">
                        <div class="col-md-6"><?php echo TranslationHandler::get_static_text("SCHOOL_EDIT"); ?></div>
                        <?php if (RightsHandler::has_user_right("COURSE_ADMINISTRATE")) { ?>
                            <div class="col-md-6"><?php echo TranslationHandler::get_static_text("COURSES"); ?></div>
                        <?php } ?>
                    </h4>
                </div>
                <hr class="widget-separator">
                <div class="widget-body">

                    <form method="post" id="update_school" action="" name="update_school_step_one" class="form-horizontal" url="edit_school.php">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="school_name"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                                <div class="col-md-7">
                                    <input class="form-control" id="edit_school_name" type="text" name="school_name" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->name : ""; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="school_address"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></label>
                                <div class="col-md-7">
                                    <input class="form-control" id="edit_school_address" type="text" name="school_address" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->address : ""; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("ZIP_CODE") . " & " . TranslationHandler::get_static_text("CITY"); ?></label>
                                <div class="col-md-2">
                                    <input class="form-control" id="edit_school_zip_code" type="text" name="school_zip_code" placeholder="<?php echo TranslationHandler::get_static_text("ZIP_CODE"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->zip_code : ""; ?>">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control" id="edit_school_city" type="text" name="school_city" placeholder="<?php echo TranslationHandler::get_static_text("CITY"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->city : ""; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?></label>
                                <div class="col-md-7">
                                    <input class="form-control" id="edit_school_phone" type="text" name="school_phone" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->phone : ""; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="school_email"><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></label>
                                <div class="col-md-7">
                                    <input class="form-control" id="edit_school_email" type="text" name="school_email" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->email : ""; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="school_max_students"><?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?></label>
                                <div class="col-md-7">
                                    <input class="form-control" id="edit_school_max_students" type="text" name="school_max_students" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->max_students : ""; ?>">
                                </div>
                            </div>
                            <?php if ($schoolHandler->_user->user_type_id == "1") { ?>
                                <div class="form-group" >
                                    <label class="col-md-3 control-label" for="school_subscription_start"><?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?></label>
                                    <div class="col-md-7">
                                        <input class="form-control"  id="edit_school_subscription_start" type="text" name="school_subscription_start" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->subscription_start : ""; ?>">
                                    </div>
                                </div>
                                <div class="form-group" >
                                    <label class="col-md-3 control-label" for="school_subscription_end"><?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?></label>
                                    <div class="col-md-7">
                                        <input class="form-control" id="edit_school_subscription_end" type="text" name="school_subscription_end" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->subscription_end : ""; ?>">
                                    </div>
                                </div>
                            <?php } else { ?>
                                <input class="form-control"  id="edit_school_subscription_start" type="hidden" name="school_subscription_start" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->subscription_start : ""; ?>">
                                <input class="form-control" id="edit_school_subscription_end" type="hidden" name="school_subscription_end" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->subscription_end : ""; ?>">
                            <?php } ?>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="school_type_id"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></label>
                                <div class="col-md-7">
                                    <select name="school_type_id" id="edit_school_type_id" class="form-control" data-plugin="select2">
                                        <?php
                                        foreach ($schoolHandler->school_types as $value) {
                                            echo '<option ';
                                            echo isset($_GET['school_id']) && $schoolHandler->school->school_type_id == $value['id'] ? "selected" : "";
                                            echo ' id="school_type_id_' . $value['id'] . '" value="' . $value['id'] . '">' . $value['title'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php if (RightsHandler::has_user_right("COURSE_ADMINISTRATE")) { ?>
                            <div class="col-md-6">
                                <table class="table table-hover">
                                    <thead>
                                    <th></th>
                                    <th><?php echo TranslationHandler::get_static_text("TITLE"); ?></th>
                                    <th><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></th>
                                    <th><?php echo TranslationHandler::get_static_text("OS"); ?></th>
                                    <th><?php echo TranslationHandler::get_static_text("SELECT"); ?></th>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($all_courses as $value) { ?>
                                            <tr style="margin-top: 30px !important;">
                                                <td style="width: 5px !important; background-color: <?php echo $value->color; ?> !important;">
                                                <td><?php echo (strlen($value->title) > 16 ? substr($value->title, 0, 16) . "..." : $value->title); ?></td>
                                                <td><?php echo (strlen($value->description) > 16 ? substr($value->description, 0, 16) . "..." : $value->description); ?></td>
                                                <td><?php echo (strlen($value->os_title) > 16 ? substr($value->os_title, 0, 16) . "..." : $value->os_title); ?></td>
                                                <td><input class="checkbox-circle a checkbox-dark" <?php
                                                    foreach ($school_courses as $n_val) {
                                                        echo $value->id == $n_val->id ? " checked " : "";
                                                    }
                                                    ?> type="checkbox" name="selected[]" value="<?php echo $value->id; ?>"></td>
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                        <div class="form-group col-md-12">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-4 pull-right">
                                <input type="hidden" id="update_school_id" name="school_id" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->id : ""; ?>">
                                <input type="hidden" name="state" value="update_school">
                                <input type="button" name="submit" id="update_school_step_one_button"
                                       value="<?php echo TranslationHandler::get_static_text("SCHOOL_UPDATE"); ?>" class="pull-right btn btn-default btn-sm update_school">   
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    ErrorHandler::show_error_page(ErrorHandler::return_error("INSUFFICIENT_RIGHTS")->title);
}
?>
<script src="assets/js/include_app.js" type="text/javascript"></script>