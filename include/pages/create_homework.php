<?php
require_once 'require.php';
require_once '../../include/handler/homeworkHandler.php';
require_once '../../include/handler/courseHandler.php';
$homeworkHandler = new HomeworkHandler();
$courseHandler = new CourseHandler();

$current_user = SessionKeyHandler::get_from_session("user", true);
$class_id = isset($_GET["class_id"]) ? $_GET["class_id"] : null;
$date = isset($_GET["date"]) ? $_GET["date"] : null;

if ($current_user->user_type_id == 1) {
    ErrorHandler::show_error_page($homeworkHandler->error);
    die();
}

?>
<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <header class="widget-header">
                <h4 class="widget-title"><?= TranslationHandler::get_static_text("CREATE_HOMEWORK") ?></h4>
            </header>
            <hr class="widget-separator">
            <div class="widget-body" style="padding-top: 32px !important;">
                <form method="post" action="" url="homework.php?step=create_homework" id="new_create_homework" name="submit_create_homework">
                    <input type="hidden" name="color" class="pick_color" value="#000000"/>
                    <div class="col-md-6">
                        <h4 class="widget-title"><?= TranslationHandler::get_static_text("HOMEWORK_INFORMATION") ?></h4>
                        <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px !important;">
                        <div class="col-md-12" style="margin-bottom: 16px !important;">
                            <div class="form-group m-b-sm">
                                <label for="available_classes" class="control-label"><?= TranslationHandler::get_static_text("ASSIGN_CLASSES") ?>:</label>
                                <select id="available_classes" name="classes[]" class="form-control" data-plugin="select2" multiple>
                                    <?php
                                    if ($homeworkHandler->get_available_classes()) {
                                        foreach ($homeworkHandler->available_classes as $value) {
                                            echo '<option value="' . $value->id . '" '. ($class_id == $value->id ? 'selected' : '') .'>' . htmlspecialchars($value->title) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group m-b-sm">
                                <label for="title" class="control-label"><?= TranslationHandler::get_static_text("TITLE") ?>:</label>
                                <input class="form-control" id="title" name="title" type="text" />
                            </div>

                            <div class="form-group m-b-sm">
                                <label for="description" class="control-label"><?= TranslationHandler::get_static_text("MESSAGE") ?>:</label>
                                <textarea class="form-control" name="description" id="description" cols="40" rows="5"></textarea>
                            </div>
                            
                            <div class="form-group m-b-sm">
                                <label for="custom"><?php echo TranslationHandler::get_static_text("COLOR"); ?></label>
                                <input type="text" id="custom" name="custom">
                            </div>
                            
                            <div class="form-group m-b-sm">
                                <label for="date_expire" class="control-label"><?= TranslationHandler::get_static_text("DEADLINE") ?>:</label>
                                <div class="col-md-12  m-b-sm" style="padding-left:0px !important; padding-right: 0px !important;">
                                    <input class="form-control" id="date_expire" type="text" name="date_expire" data-options="{format: 'YYYY-MM-DD', showTodayButton:false}" data-plugin="datetimepicker" value=<?= $date ?> />
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h4 class="widget-title"><?= TranslationHandler::get_static_text("ASSIGN_HOMEWORK") ?></h4>
                        <hr class="m-0 m-b-md" style="border-color: #ddd;margin: 16px 0px 24px 0px !important;">
                        <?php foreach($courseHandler->get_os_options() as $value) { ?>
                        <div class="panel-group accordion" id="accordion" style="margin-bottom: 30px !important;">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="heading-<?= htmlspecialchars($value["title"]) ?>">
                                    <a class="accordion-toggle collapsed" style="padding: 0px 0.75rem 0px 0.75rem !important;" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-<?= htmlspecialchars($value["title"]) ?>" aria-expanded="false" aria-controls="collapse-<?= htmlspecialchars($value["title"]) ?>">
                                        <h4 class="panel-title" style="color: #6a6c6f !important;text-transform:none !important;"><?= htmlspecialchars($value["title"]) ?> <?= TranslationHandler::get_static_text("USERS") ?></h4>
                                        <i class="fa acc-switch" style="padding-right: 40px;"></i>
                                    </a>
                                </div>
                                <div id="collapse-<?= htmlspecialchars($value["title"]) ?>" class="panel-collapse collapse" aria-labelledby="heading-<?= htmlspecialchars($value["title"]) ?>" aria-expanded="false">
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
                                                            <input name="course[]" class="check_course_elements" checkbox_id="<?= $unique_token ?>" type="checkbox" id="checkbox-<?= htmlspecialchars($course->title) .'-'. $course->id ?>"> <label for="checkbox-<?= htmlspecialchars($course->title) .'-'. $course->id ?>"></label>
                                                        </div>
                                                        <div style="float:left;width:95%;border-left: 5px solid <?= $course->color ?>;">
                                                        <a class="accordion-toggle collapsed" style="padding: 0px 0px 0px 10px !important;" role="button" data-toggle="collapse" data-parent="#accordion-<?= $unique_token ?>" href="#collapse-<?= $unique_token ?>" aria-expanded="false" aria-controls="collapse-<?= $unique_token ?>">
                                                            <span class="text-color fw-500"><?= htmlspecialchars($course->title) ?></span>
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
                                                                    <div class="text-color fw-500" style="margin-bottom:5px;"><?= TranslationHandler::get_static_text("LECTURES") ?>:</div>
                                                                    <?php 
                                                                    if($courseHandler->get_for_school($current_user->school_id, $course->id, "lecture", $value["id"])) {
                                                                        foreach($courseHandler->lectures as $lecture) { ?>
                                                                        <div class="panel-group" style="margin-bottom:0px !important;line-height:1.5 !important;">
                                                                            <div class="panel panel-default">
                                                                                <div class="panel-heading">
                                                                                    <div class="checkbox" style="float:left;width:5%;">
                                                                                        <input name="lecture[]" type="checkbox" id="checkbox-<?= htmlspecialchars($lecture->title) .'-'. $lecture->id ?>" value="<?= $lecture->id ?>"> <label for="checkbox-<?= htmlspecialchars($lecture->title) .'-'. $lecture->id ?>"></label>
                                                                                    </div>
                                                                                    <div style="float:left;width:90%;">
                                                                                    <span class="text-color fw-500" style="margin-left:5px;"><?= htmlspecialchars($lecture->title) ?></span>
                                                                                    </div>
                                                                                    <div style="clear:both;"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div style="clear:both;"></div>
                                                                    <?php } } ?>
                                                                </div>
                                                                <div style="float:left;width:50%;">
                                                                    <div class="text-color fw-500" style="margin-bottom:5px;"><?= TranslationHandler::get_static_text("TESTS") ?>:</div>
                                                                    <?php 
                                                                    if($courseHandler->get_for_school($current_user->school_id, $course->id, "test", $value["id"])) {
                                                                        foreach($courseHandler->tests as $test) { ?>
                                                                        <div class="panel-group" style="margin-bottom:0px !important;line-height:1.5 !important;">
                                                                            <div class="panel panel-default">
                                                                                <div class="panel-heading">
                                                                                    <div class="checkbox" style="float:left;width:5%;">
                                                                                        <input name="test[]" type="checkbox" id="checkbox-<?= htmlspecialchars($test->title) .'-'. $test->id ?>" value="<?= $test->id ?>"> <label for="checkbox-<?= htmlspecialchars($test->title) .'-'. $test->id ?>"></label>
                                                                                    </div>
                                                                                    <div style="float:left;width:90%;">
                                                                                    <span class="text-color fw-500" style="margin-left:5px;"><?= htmlspecialchars($test->title) ?></span>
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