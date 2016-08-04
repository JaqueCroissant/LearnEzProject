<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
$classHandler = new ClassHandler();
$schoolHandler = new SchoolHandler();
$schoolHandler->get_all_schools();
$classHandler->get_all_classes();
?>

<div class="row">   
    <div class="col-md-12">
        <div class="widget">
            <?php
            if (RightsHandler::has_user_right("CLASS_EDIT")) {
                if (isset($_GET['class_id'])) {
                    $classHandler->get_class_by_id($_GET['class_id']);
                } else {
                    ErrorHandler::show_error_page();
                }
                ?>
                <div class="widget-header">
                    <h1 class="widget-title"><?php echo TranslationHandler::get_static_text("EDIT_CLASS_GENERIC"); ?></h1>
                </div>
                <hr class="widget-separator">
                <div class="widget-body">
                    <form method="post" id="update_class_form" name="update_class" action="" class="form-horizontal" url="edit_class.php">
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-3 control-label" for="class_title"><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></label>
                            <div class="col-md-4">
                                <input class="form-control" id="class_title" type="text" name="class_title" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?>" value="<?php echo isset($_GET['class_id']) ? $classHandler->school_class->title : ""; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-3 control-label" for="class_open"><?php echo TranslationHandler::get_static_text("OPEN"); ?></label>
                            <div class="col-md-4">
                                <div class="checkbox">
                                    <input type="hidden" name="class_open" id="class_open_hidden">
                                    <input class="checkbox-circle checkbox-dark" <?php echo (isset($_GET['class_id']) && $classHandler->school_class->open == "1") ? "checked" : ""; ?> type="checkbox" name="class_open_checkbox" id="class_open">
                                    <label for="class_open"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-3 control-label" for="class_begin"><?php echo TranslationHandler::get_static_text("BEGIN"); ?></label>
                            <div class="col-md-4">
                                <input class="form-control" type="text" id="class_begin" name="class_begin" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("BEGIN"); ?>" value="<?php echo isset($_GET['class_id']) ? $classHandler->school_class->start_date : ""; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-3 control-label" for="class_end"><?php echo TranslationHandler::get_static_text("END"); ?></label>
                            <div class="col-md-4">
                                <input class="form-control" type="text" id="class_end" name="class_end" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("END"); ?>" value="<?php echo isset($_GET['class_id']) ? $classHandler->school_class->end_date : ""; ?>">
                            </div>
                        </div>
                        <?php
                        if ($classHandler->_user->user_type_id == 1) {
                            ?>
                            <div class="form-group">
                                <label class="col-sm-2 col-sm-offset-3 control-label" for="school_id"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                                <div class="col-md-4">
                                    <select id="select_school" name="school_id" class="form-control" data-options="{minimumResultsForSearch: 5}" data-plugin="select2">
                                        <?php
                                        if (count($schoolHandler->all_schools) > 0) {
                                            foreach ($schoolHandler->all_schools as $value) {
                                                echo '<option ' . (isset($_GET['class_id']) && $classHandler->school_class->school_id == $value->id ? "selected" : "") . ' id="school_id_' . $value->id . '" value="' . $value->id . '">' . $value->name . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                        } else {
                            echo '<input id="school_id" type="hidden" name="school_id" value="' . $classHandler->_user->school_id . '">';
                        }
                        ?>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-3 control-label" for="class_description"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></label>
                            <div class="col-md-4">
                                <input type="hidden" id="hidden_description" name="class_description">
                                <textarea form="update_class" class="form-control" type="text" id="class_description" placeholder="<?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?>"><?php echo isset($_GET['class_id']) ? $classHandler->school_class->description : ""; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-3 control-label"></label>
                            <div class="col-md-4">
                                <input type="hidden" id="update_class_id" name="class_id" value="<?php echo (isset($_GET['class_id']) ? $_GET['class_id'] : ""); ?>">
                                <input type="hidden" name="state" value="update_class">
                                <input type="button" name="submit" id="update_class"
                                       value="<?php echo TranslationHandler::get_static_text("ACCEPT"); ?>" class="pull-right btn btn-default btn-sm update_class">   
                            </div>
                        </div>

                    </form>
                    <?php
                } else {
                    echo ErrorHandler::return_error("INSUFFICIENT_RIGHTS")->title;
                }
                ?>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>
<!--Link til datetimepicker options-->
<!--https://eonasdan.github.io/bootstrap-datetimepicker/Options/#format-->