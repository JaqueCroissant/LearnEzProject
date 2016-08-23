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
        <div class="col-sm-12">
            <div class="widget">
                <div class="widget-body">
                    <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("SCHOOL_EDIT"); ?></h4>
                    <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                    <form method="post" id="update_school" action="" name="update_school_step_one" class="form-horizontal" url="edit_school.php">
                        <div class="col-sm-6">

                            <div class="col-md-12">
                                <div class="m-b-sm">
                                    <label for="school_name"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                                    <input class="form-control" id="edit_school_name" type="text" name="school_name" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->name : ""; ?>">
                                </div>
                                <div class="m-b-sm">
                                    <label for="school_address"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></label>
                                    <input class="form-control" id="edit_school_address" type="text" name="school_address" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->address : ""; ?>">
                                </div>
                                <div class="m-b-sm">
                                    <label for="school_phone"><?php echo TranslationHandler::get_static_text("ZIP_CODE") . " & " . TranslationHandler::get_static_text("CITY"); ?></label>
                                    <div style="display:table; width:100%;">
                                        <input class="form-control" style="width:24%;display:table-cell;" id="edit_school_zip_code" type="text" name="school_zip_code" placeholder="<?php echo TranslationHandler::get_static_text("ZIP_CODE"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->zip_code : ""; ?>">
                                        <input class="form-control" style="margin-left:2%;width:74%;display:table-cell;" id="edit_school_city" type="text" name="school_city" placeholder="<?php echo TranslationHandler::get_static_text("CITY"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->city : ""; ?>">
                                    </div>
                                </div>
                                <div class="m-b-sm">
                                    <label for="school_phone"><?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?></label>
                                    <input class="form-control" id="edit_school_phone" type="text" name="school_phone" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->phone : ""; ?>">
                                </div>
                                <div class="m-b-sm">
                                    <label for="school_email"><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></label>
                                    <input class="form-control" id="edit_school_email" type="text" name="school_email" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->email : ""; ?>">
                                </div>
                                <div class="m-b-sm">
                                    <label for="school_max_students"><?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?></label>
                                    <input class="form-control" id="edit_school_max_students" type="text" name="school_max_students" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->max_students : ""; ?>">
                                </div>
                                <?php if ($schoolHandler->_user->user_type_id == "1") { ?>
                                    <div class="m-b-sm" >
                                        <label for="school_subscription_start"><?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?></label>
                                        <input class="form-control"  id="edit_school_subscription_start" type="text" name="school_subscription_start" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->subscription_start : ""; ?>">
                                    </div>
                                    <div class="m-b-sm" >
                                        <label for="school_subscription_end"><?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?></label>
                                        <input class="form-control" id="edit_school_subscription_end" type="text" name="school_subscription_end" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->subscription_end : ""; ?>">
                                    </div>
                                <?php } else { ?>
                                    <input class="form-control"  id="edit_school_subscription_start" type="hidden" name="school_subscription_start" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->subscription_start : ""; ?>">
                                    <input class="form-control" id="edit_school_subscription_end" type="hidden" name="school_subscription_end" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?>" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->subscription_end : ""; ?>">
                                <?php } ?>
                                <div class="m-b-sm">
                                    <label for="school_type_id"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></label>
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
                            <div class="col-sm-6">
                                <div class="panel-group accordion" id="accordion-edit-courses" role="tablist" aria-multiselectable="false">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="heading-edit-courses">
                                            <a class="accordion-toggle" style="padding: 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion-edit-courses" href="#collapse-edit-courses" aria-expanded="false" aria-controls="accordion-edit-courses">
                                                <label for="textarea1" style="cursor:pointer;"><?php echo TranslationHandler::get_static_text("COURSE_CHOOSE"); ?></label>
                                                <i class="fa acc-switch"></i>
                                            </a>
                                        </div>
                                        <div id="collapse-edit-courses" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-edit-courses" aria-expanded="false">
                                            <hr class="m-0 " style="border-color: #ddd;margin: 2px 0px 14px 0px !important;">
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
                                                        <tr class="course_table_choose" style="margin-top: 30px !important;">
                                                            <td style="width: 5px !important; background-color: <?php echo $value->color; ?> !important;">
                                                            <td><?php echo (strlen($value->title) > 16 ? substr($value->title, 0, 16) . "..." : $value->title); ?></td>
                                                            <td><?php echo (strlen($value->description) > 16 ? substr($value->description, 0, 16) . "..." : $value->description); ?></td>
                                                            <td><?php echo (strlen($value->os_title) > 16 ? substr($value->os_title, 0, 16) . "..." : $value->os_title); ?></td>
                                                            <td>
                                                                <div class="checkbox">
                                                                    <input id="course_select_<?= $value->title ?>" class="checkbox-default a checkbox-dark course_table_select" <?php
                                                                    foreach ($school_courses as $n_val) {
                                                                        echo $value->id == $n_val->id ? " checked " : "";
                                                                    }
                                                                    ?> type="checkbox" name="selected[]" value="<?php echo $value->id; ?>">
                                                                    <label for="course_select_<?= $value->title ?>"></label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-sm-6 m-b-lg">
                            <div class="panel-group accordion" id="accordion-new-thumbnail-course" role="tablist" aria-multiselectable="false">
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="heading-new-thumbnail-course">
                                        <a class="accordion-toggle" style="padding: 0px !important;" role="button" data-toggle="collapse" data-parent="#accordion-new-thumbnail-course" href="#collapse-new-thumbnail-course" aria-expanded="false" aria-controls="collapse-new-thumbnail-course">
                                            <label for="textarea1" style="cursor:pointer;"><?php echo TranslationHandler::get_static_text("UPLOAD_IMAGE"); ?></label>
                                            <i class="fa acc-switch"></i>
                                        </a>
                                    </div>
                                    <div id="collapse-new-thumbnail-course" class="panel-collapse collapse p-b-lg" role="tabpanel" aria-labelledby="heading-new-thumbnail-course" aria-expanded="false">
                                        <hr class="m-0 " style="border-color: #ddd;margin: 2px 0px 14px 0px !important;">
                                        <div class="col-sm-12 m-b-lg">
                                            <input type='hidden' id="school_id" value="<?php echo isset($_GET['school_id']) && !empty($schoolHandler->school->id) ? $schoolHandler->school->id : ""; ?>">
                                            <input style="width:64%;display:inline;" type="file" id="school_image" name="school_image" class="btn btn-default text-left" style='width: 100%;'>
                                            <input style="margin-left:1%;width:34%;display:inline;height:40.33px;" type="button" name="submit" id="edit_school_image" value="<?php echo TranslationHandler::get_static_text("UPLOAD_IMAGE"); ?>" class="pull-right btn btn-default upload_school_image">
                                            <div class="hidden school_thumbnail_upload_trans"><?= TranslationHandler::get_static_text("UPLOADING") ?></div>
                                            <div class="hidden school_thumbnail_new_trans"><?= TranslationHandler::get_static_text("UPLOAD_IMAGE") ?></div>
                                        </div>
                                        <div style="clear:both;"></div>
                                        <div class="center" style="width:90px;">
                                            <div class="thumbnail_element" style="cursor:pointer;z-index:10;">

                                                <div class="delete_thumbnail_style delete_school_thumbnail hidden" style="right:initial !important;" title="<?= TranslationHandler::get_static_text("DELETE_IMAGE") ?>">
                                                    <i class="zmdi zmdi-close" style="display:initial !important;"></i>
                                                </div>
                                                <img class="school_thumbnail" src="<?= $schoolHandler->school->filename != "" ? $schoolHandler->school->filename : "" ?>"/>                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="position:absolute; bottom:40px;right:30px;">
                            <div class="">
                                <input type="hidden" id="update_school_id" name="school_id" value="<?php echo isset($_GET['school_id']) ? $schoolHandler->school->id : ""; ?>">
                                <input type="hidden" name="state" value="update_school">
                                <input type="button" name="submit" id=""
                                       value="<?php echo TranslationHandler::get_static_text("SCHOOL_UPDATE"); ?>" class="pull-right btn btn-default update_school">   
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
<script>
    $(document).on("click", ".course_table_choose", function (event) {
        if (!$(event.target).closest(".course_table_select").length) {
            var checkbox = $(this).find(".course_table_select");
            checkbox.prop("checked") ? checkbox.prop("checked", false) : checkbox.prop("checked", true);
        }
    });
</script>