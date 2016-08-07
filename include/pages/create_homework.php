<?php
require_once 'require.php';
require_once '../../include/handler/homeworkHandler.php';
require_once '../../include/handler/courseHandler.php';
$homeworkHandler = new HomeworkHandler();
$courseHandler = new CourseHandler();

$current_user = SessionKeyHandler::get_from_session("user", true);
$class_id = isset($_GET["class_id"]) ? $_GET["class_id"] : null;
$student_id = isset($_GET["student_id"]) ? $_GET["student_id"] : null;
if(SessionKeyHandler::get_from_session("user", true)->user_type_id == 1) {
    ErrorHandler::show_error_page();
    die();
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <header class="widget-header">
                <h4 class="widget-title">Opret lektier</h4>
            </header>
            <hr class="widget-separator">
            <div class="widget-body" style="padding-top: 32px !important;">
                <form method="post" action="" url="homework.php?step=create_homework" id="new_create_homework" name="submit_create_homework">
                    <div class="col-md-6">
                        <h4 class="widget-title">Vælg modtager for lektier</h4>
                        <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                        <div class="col-md-12" style="margin-bottom: 16px !important;">
                            <div class="form-group m-b-sm">
                                <label for="available_classes" class="control-label">Klasser:</label>
                                <select id="available_classes" name="classes[]" class="form-control" data-plugin="select2" multiple>
                                    <?php
                                    if ($homeworkHandler->get_available("classes")) {
                                        foreach ($homeworkHandler->available_classes as $value) {
                                            echo '<option value="' . $value->id . '" '. ($class_id == $value->id ? 'selected' : '') .'>' . $value->title . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group m-b-sm">
                                <label for="available_students" class="control-label">Elever:</label>
                                <select id="available_students" name="students[]" class="form-control" data-plugin="select2" multiple>
                                    <?php
                                    if ($homeworkHandler->get_available()) {
                                        foreach ($homeworkHandler->available_students as $value) {
                                            echo '<option value="' . $value->id . '" '. ($student_id == $value->id ? 'selected' : '') .'>' . $value->firstname . ' ' . $value->surname . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group m-b-sm">
                                <label for="date_expire" class="control-label">Besked:</label>
                                <textarea class="form-control" name="description" id="description" cols="40" rows="5"></textarea>

                            </div>
                            
                            <div class="form-group m-b-sm">
                                <label for="date_expire" class="control-label">Deadline:</label>
                                <div class="col-md-12  m-b-sm" style="padding-left:0px !important; padding-right: 0px !important;">
                                    <input class="form-control" id="date_expire" type="text" name="date_expire" data-options="{format: 'YYYY-MM-DD', showTodayButton:false}" data-plugin="datetimepicker" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h4 class="widget-title">Tildel lektier</h4>
                        <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px 24px 0px !important;">
                        <?php foreach($courseHandler->get_os_options() as $value) { ?>
                        <div class="panel-group accordion" id="accordion" style="margin-bottom: 30px !important;">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading-<?= $value["title"] ?>">
                                    <a class="accordion-toggle collapsed" style="padding: 0px 0.75rem 0px 0.75rem !important;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?= $value["title"] ?>" aria-expanded="false" aria-controls="collapse-<?= $value["title"] ?>">
                                        <h4 class="panel-title" style="color: #6a6c6f !important;text-transform:none !important;"><?= $value["title"] ?> brugere</h4>
                                        <i class="fa acc-switch" style="padding-right: 40px;"></i>
                                    </a>
                                </div>
                                <div id="collapse-<?= $value["title"] ?>" class="panel-collapse collapse" aria-labelledby="heading-<?= $value["title"] ?>" aria-expanded="false">
                                    <div class="panel-body" style="margin: 0px -4px 0px -4px !important;">
                                        <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 0px 0px 8px 0px !important;">
                                        <?php 
                                        if($courseHandler->get_for_school($current_user->school_id, 0, "course", $value["id"])) {
                                            foreach($courseHandler->courses as $course) {
                                            $unique_token = md5(uniqid(mt_rand(), true)); ?>
                                            
                                            <div class="panel-group accordion" id="accordion-<?= $unique_token ?>" style="margin-bottom:0px !important;">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="heading-<?= $unique_token ?>">
                                                        <div class="checkbox" style="float:left;width:5%;">
                                                            <input name="course[]" class="check_course_elements" checkbox_id="<?= $unique_token ?>" type="checkbox" id="checkbox-<?= $course->title .'-'. $course->id ?>"> <label for="checkbox-<?= $course->title .'-'. $course->id ?>"></label>
                                                        </div>
                                                        <div style="float:left;width:95%;border-left: 5px solid <?= $course->color ?>;">
                                                        <a class="accordion-toggle collapsed" style="padding: 0px 0px 0px 10px !important;" role="button" data-toggle="collapse" data-parent="#accordion-<?= $unique_token ?>" href="#collapse-<?= $unique_token ?>" aria-expanded="false" aria-controls="collapse-<?= $unique_token ?>">
                                                            <span class="text-color fw-500"><?= $course->title ?></span>
                                                            <i class="fa acc-switch" style="padding-right: 20px;line-height:2 !important;"></i>
                                                        </a>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                    <div id="collapse-<?= $unique_token ?>" class="panel-collapse collapse" aria-labelledby="heading-<?= $unique_token ?>" aria-expanded="false">
                                                        <div class="panel-body" style="margin: 0px !important;padding: 0px !important;">
                                                            <hr class="m-0 m-b-md" style="float:right;width:95%;border-color: #ddd;margin: 0px 0px 8px 0px !important;">
                                                            <div style="clear:both;"></div>
                                                            <div style="float:right;width:95%;padding-bottom:20px;">
                                                                <div style="float:left;width:50%;">
                                                                    <div class="text-color fw-500" style="margin-bottom:5px;">Lektioner:</div>
                                                                    <?php 
                                                                    if($courseHandler->get_for_school($current_user->school_id, $course->id, "lecture", $value["id"])) {
                                                                        foreach($courseHandler->lectures as $lecture) { ?>
                                                                        <div class="panel-group" style="margin-bottom:0px !important;line-height:1.5 !important;">
                                                                            <div class="panel panel-default">
                                                                                <div class="panel-heading">
                                                                                    <div class="checkbox" style="float:left;width:5%;">
                                                                                        <input name="lecture[]" type="checkbox" id="checkbox-<?= $lecture->title .'-'. $lecture->id ?>" value="<?= $lecture->id ?>"> <label for="checkbox-<?= $lecture->title .'-'. $lecture->id ?>"></label>
                                                                                    </div>
                                                                                    <div style="float:left;width:90%;">
                                                                                    <span class="text-color fw-500" style="margin-left:5px;"><?= $lecture->title ?></span>
                                                                                    </div>
                                                                                    <div style="clear:both;"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div style="clear:both;"></div>
                                                                    <?php } } ?>
                                                                </div>
                                                                <div style="float:left;width:50%;">
                                                                    <div class="text-color fw-500" style="margin-bottom:5px;">Test:</div>
                                                                    <?php 
                                                                    if($courseHandler->get_for_school($current_user->school_id, $course->id, "test", $value["id"])) {
                                                                        foreach($courseHandler->tests as $test) { ?>
                                                                        <div class="panel-group" style="margin-bottom:0px !important;line-height:1.5 !important;">
                                                                            <div class="panel panel-default">
                                                                                <div class="panel-heading">
                                                                                    <div class="checkbox" style="float:left;width:5%;">
                                                                                        <input name="test[]" type="checkbox" id="checkbox-<?= $test->title .'-'. $test->id ?>" value="<?= $test->id ?>"> <label for="checkbox-<?= $test->title .'-'. $test->id ?>"></label>
                                                                                    </div>
                                                                                    <div style="float:left;width:90%;">
                                                                                    <span class="text-color fw-500" style="margin-left:5px;"><?= $test->title ?></span>
                                                                                    </div>
                                                                                    <div style="clear:both;"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div style="clear:both;"></div>
                                                                    <?php } } ?>
                                                                </div><div style="clear:both;"></div>
                                                            </div><div style="clear:both;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>

                    <div style="clear:both"></div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="button" name="submit" id="create_homework_button" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="pull-right btn btn-default submit_create_homework">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>