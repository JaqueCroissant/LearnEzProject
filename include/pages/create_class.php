<?php
require_once 'require.php';
require_once '../../include/handler/classHandler.php';
require_once '../../include/handler/schoolHandler.php';
$schoolHandler = new SchoolHandler();
$schoolHandler->get_all_schools();
$classHandler = new ClassHandler();
?>
<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <header class="widget-header">
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("CLASS_CREATE_NEW"); ?></h4>
            </header>
            <hr class="widget-separator">
            <div class="widget-body">
                <div id="step_one">
                    <form method="post" id="create_class_form" name="create_class" action="" class="form-horizontal" url="create_class.php">
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="class_title"><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control" type="text" name="class_title" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?>">
                            </div>
                        </div>
                        <?php
                        if ($classHandler->_user->user_type_id == 1) {
                            ?>
                            <div class="form-group">
                                <label class="col-sm-2 col-sm-offset-2 control-label" for="school_id"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                                <div class="col-md-5">
                                    <select id="select_school" name="school_id" class="form-control" data-plugin="select2">
                                        <?php
                                        if (count($schoolHandler->all_schools) > 0) {
                                            foreach ($schoolHandler->all_schools as $value) {
                                                echo '<option value="' . $value->id . '">' . $value->name . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                        } else {
                            echo '<input type="hidden" name="school_id" value="' . $classHandler->_user->school_id . '">';
                        }
                        ?>

                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="class_open"><?php echo TranslationHandler::get_static_text("OPEN"); ?></label>
                            <div class="col-md-5">
                                <div class="checkbox">
                                    <input class="checkbox-circle checkbox-dark" checked="" type="checkbox" name="class_open" id="class_open">
                                    <label for="class_open"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="class_begin"><?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control" type="text" id="class_begin" name="class_begin" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="class_end"><?php echo TranslationHandler::get_static_text("CLASS_END"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control" type="text" id="class_end" name="class_end" data-options="{format: 'YYYY/MM/DD', showTodayButton:true}" data-plugin="datetimepicker" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_END"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label" for="class_description"><?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?></label>
                            <div class="col-md-5">
                                <input type="hidden" id="hidden_description" name="class_description">
                                <textarea form="create_class" class="form-control" type="text" id="class_description" name="class_description" placeholder="<?php echo TranslationHandler::get_static_text("INFO_DESCRIPTION"); ?>"></textarea>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-offset-2 control-label"></label>
                            <div class="col-md-5">
                                <input type="hidden" name="step" id="create_class_step">
                                <input type="button" name="submit" id="create_class_step_one_button" step="1"
                                       value="<?php echo TranslationHandler::get_static_text("CREATE_CLASS"); ?>" class="pull-right btn btn-default create_class">   
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="assets/js/include_app.js" type="text/javascript"></script>