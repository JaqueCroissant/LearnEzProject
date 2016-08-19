<?php
require_once 'require.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/courseHandler.php';
$schoolHandler = new SchoolHandler();
$courseHandler = new CourseHandler();
if (!RightsHandler::has_user_right("SCHOOL_CREATE")) {
    ErrorHandler::show_error_page(ErrorHandler::return_error("INSUFFICIENT_RIGHTS")->title);
}
$courseHandler->get_multiple(0, "course");

$schoolHandler->get_school_types();
?>
<div class="row">
    <div class="col-md-12">
        <div class="widget">

            <header class="widget-header">
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("SCHOOL_CREATE_NEW"); ?></h4>
            </header>
            <hr class="widget-separator">
            <div class="widget-body">
                <div id="step_one">
                    <form method="post" id="create_school_step_one" action="" name="create_school_step_one" class="form-horizontal" url="create_school.php">
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_name"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control" type="text" name="school_name" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_address"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control" type="text" name="school_address" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("ZIP_CODE") . " & " . TranslationHandler::get_static_text("CITY"); ?></label>
                            <div class="col-md-1">
                                <input class="form-control" type="text" name="school_zip_code" placeholder="<?php echo TranslationHandler::get_static_text("ZIP_CODE"); ?>">
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="school_city" placeholder="<?php echo TranslationHandler::get_static_text("CITY"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control" type="text" name="school_phone" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_email"><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control" type="text" name="school_email" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_type_id"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></label>
                            <div class="col-md-5">
                                <select id="select_school_type" name="school_type_id" class="form-control" data-plugin="select2">
                                    <?php
                                    foreach ($schoolHandler->school_types as $value) {
                                        echo '<option value="' . $value['id'] . '">' . htmlspecialchars($value['title']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label"></label>
                            <div class="col-md-5">
                                <input type="hidden" name="step" id="create_school_hidden_field_step_1">
                                <input type="button" name="submit" id="create_school_step_one_button" step="1" 
                                       value="<?php echo TranslationHandler::get_static_text("SCHOOL_FINISH_STEP_ONE"); ?>" class="pull-right btn btn-default btn-sm create_school">   
                            </div>
                        </div>
                    </form>
                </div>


                <div id="step_two" class="hidden">
                    <form method="POST" id="create_school_step_two" class="form-horizontal" action="" url="create_school.php">
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_max_students"><?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control" type="text" name="school_max_students" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?>">
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_subscription_start"><?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control datepickers" id="school_subscription_start" type="text" name="school_subscription_start" placeholder="<?php echo TranslationHandler::get_static_text("SUBSCRIPTION_START"); ?>">
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_subscription_end"><?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control datepickers" id="school_subscription_end" type="text" name="school_subscription_end" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label"></label>
                            <div class="col-md-5">
                                <input type="hidden" name="step" id="create_school_hidden_field_step_2">
                                <input type="button" name="submit" id="create_school_step_two_button" step="2"
                                       value="<?php echo TranslationHandler::get_static_text("SCHOOL_FINISH_STEP_TWO"); ?>" class="pull-right btn btn-default btn-sm create_school">     
                            </div>
                        </div>


                    </form>
                </div>

                <div id="step_three" class="hidden">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <form class="form-horizontal" method="POST" id="school_image_upload" action="" url='create_school.php' enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label" for='school_image'><?php echo TranslationHandler::get_static_text("UPLOAD_IMAGE") ?></label>
                                        <div class="col-md-8">
                                            <input type='hidden' id="school_id" value="">
                                            <input type="file" id="school_image" name="school_image" class="p-h-xs btn btn-default text-left" style='width: 100%;'>
                                        </div>
                                    </div>
                                    <div class="form-group p-t-md">
                                        <label class="col-md-3 control-label"></label>
                                        <div class="col-md-8">
                                            <input type="button" name="submit" id="upload_school_image"
                                                   value="<?php echo TranslationHandler::get_static_text("UPLOAD_IMAGE"); ?>" class="pull-right btn btn-default btn-sm upload_school_image">     
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-6">
                                <form method="POST" id="assign_course_to_school" url="create_school.php" name="create_school_step_3">
                                    <table class="table table-hover">
                                        <thead>
                                        <th></th>
                                        <th><?php echo TranslationHandler::get_static_text("TITLE"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("OS"); ?></th>
                                        <th><?php echo TranslationHandler::get_static_text("SELECT"); ?></th>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($courseHandler->courses as $value) { ?>
                                                <tr style="margin-top: 30px !important;">
                                                    <td style="width: 5px !important; background-color: <?php echo $value->color; ?> !important;">
                                                    <td><?php echo (strlen(htmlspecialchars($value->title)) > 16 ? substr(htmlspecialchars($value->title), 0, 16) . "..." : htmlspecialchars($value->title)); ?></td>
                                                    <td><?php echo (strlen(htmlspecialchars($value->description)) > 16 ? substr(htmlspecialchars($value->description), 0, 16) . "..." : htmlspecialchars($value->description)); ?></td>
                                                    <td><?php echo (strlen(htmlspecialchars($value->os_title)) > 16 ? substr(htmlspecialchars($value->os_title), 0, 16) . "..." : htmlspecialchars($value->os_title)); ?></td>
                                                    <td><input class="checkbox-circle a checkbox-dark" type="checkbox" name="selected[]" value="<?php echo $value->id; ?>"></td>
                                                </tr>
                                            <?php } ?>

                                        </tbody>
                                    </table>
                                    <div class="form-group p-t-md">
                                        <label class="col-md-2 col-md-offset-5 control-label"></label>
                                        <div class="col-md-5">
                                            <input type="hidden" name="step" id="create_school_hidden_field_step_3">
                                            <input type="button" name="submit" id="create_school_step_three_button" step="3"
                                                   value="<?php echo TranslationHandler::get_static_text("SCHOOL_FINISH_STEP_THREE"); ?>" class="pull-right btn btn-default btn-sm create_school">     
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="input-container">
                    <div class="center col-md-8 p-t-xl">
                        <div class="progress">
                            <div id="step_one_progress" class="progress-bar" style="width: 33.33%;"><span>Step 1</span></div>
                            <div id="step_two_progress" class="progress-bar-inactive" style="width: 33.33%;"><span>Step 2</span></div>
                            <div id="step_three_progress" class="progress-bar-inactive" style="width: 33.33%; float: left;"><span>Step 3</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".datepickers").datepicker( {
        dateFormat: "yy-mm-dd"
    });
</script>