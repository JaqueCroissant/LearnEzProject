<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/classHandler.php';
$classHandler = SessionKeyHandler::get_from_session("class_handler", true);
$classHandler->get_year_and_year_prefix();
?>

<div class="col-md-12">
    <div class="widget">
        <header class="widget-header">
            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("CLASS_CREATE_NEW"); ?></h4>
        </header>
        <hr class="widget-separator">
        <div class="widget-body">
            <div id="step_one">
                <form method="post" id="create_school_step_one" action="" class="form-horizontal" url="create_school.php">
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label" for="class_title"><?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?></label>
                        <div class="col-md-5">
                            <input class="form-control input-sm" type="text" name="class_title" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label" for="class_year_of"><?php echo TranslationHandler::get_static_text("CLASS_YEAR_OF"); ?></label>
                        <div class="col-md-5">
                            <label class="col-sm-2 control-label" for="class_prefix_id"><?php echo TranslationHandler::get_static_text("CLASS_YEAR_PREFIX"); ?></label>
                            <div class="col-sm-4">
                                <select name="class_prefix_id" id="class_prefix_id" class="form-control input-sm">
                                    <?php
                                    foreach ($classHandler->year_prefixes as $value) {
                                        echo '<option value="' . $value['id'] . '">' . $value['title'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <label class="col-sm-2 control-label" for="class_year_id"><?php echo TranslationHandler::get_static_text("CLASS_YEAR"); ?></label>
                            <div class=" col-sm-4">
                                <select name="class_year_id" id="class_prefix_id" class="form-control input-sm">
                                    <?php
                                    foreach ($classHandler->years as $value) {
                                        echo '<option value="' . $value['id'] . '">' . $value['year'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label" for="school"><?php echo "Skolen: To do - hentes dynamisk alt efter user type"; ?></label>
                        <div class="col-md-5">
                            <input class="form-control input-sm" readonly="true" type="text" name="school" value="1">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label" for="class_open"><?php echo TranslationHandler::get_static_text("CLASS_OPEN"); ?></label>
                        <div class="col-md-5">
                            <div class="checkbox">
                                <input class="checkbox-circle checkbox-dark" checked="" style="opacity: 1;" type="checkbox" name="class_open" id="class_open">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label" for="class_begin"><?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?></label>
                        <div class="col-md-5">
                            <input class="form-control input-sm" type="text" id="class_begin" name="class_begin" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_BEGIN"); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label" for="class_end"><?php echo TranslationHandler::get_static_text("CLASS_END"); ?></label>
                        <div class="col-md-5">
                            <input class="form-control input-sm" type="text" id="class_end" name="class_end" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_END"); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label"></label>
                        <div class="col-md-5">
                            <input type="hidden" name="step" id="create_class_step">
                            <input type="button" name="submit" id="create_class_step_one_button" step="1" 
                                   value="<?php echo TranslationHandler::get_static_text("CREATE_CLASS"); ?>" class="pull-right btn btn-default btn-sm create_school">   
                        </div>
                    </div>
                    
                </form>
            </div>

            <div id="step_two" hidden="true">
                <div class="form-group">
                    <label class="col-sm-2 col-sm-offset-2 control-label" for="class_title"><?php echo "Tildel elever og lærere til den nyligt oprettede klasse"; ?></label>
                    <!--                    <div class="col-md-5">
                                            <input class="form-control input-sm" type="text" name="class_title" placeholder="<?php echo TranslationHandler::get_static_text("CLASS_TITLE"); ?>">
                                        </div>-->
                </div>

                <div class="form-group">
                    <label class="col-sm-2 col-sm-offset-2 control-label"></label>
                    <div class="col-md-5">
                        <input type="hidden" name="step" id="create_class_step_2">
                        <input type="button" name="submit" id="create_class_step_two_button" step="2" 
                               value="<?php echo TranslationHandler::get_static_text("CREATE_CLASS"); ?>" class="pull-right btn btn-default btn-sm create_school">   
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function () {
        $("#class_begin").datepicker();
        $("#class_end").datepicker();
    });
</script>