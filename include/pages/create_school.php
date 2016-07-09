<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';
$schoolHandler = SessionKeyHandler::get_from_session("school_handler", true);
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
                    <form method="post" id="create_school" action="" name="create_school" class="form-horizontal" url="create_school.php">
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_name"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control input-sm" type="text" name="school_name" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_address"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control input-sm" type="text" name="school_address" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control input-sm" type="text" name="school_phone" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_email"><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control input-sm" type="text" name="school_email" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_type_id"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></label>
                            <div class="col-md-5">
                                <select name="school_type_id" class="form-control input-sm">
                                    <?php
                                    foreach ($schoolHandler->school_types as $value) {
                                        echo '<option value="' . $value['id'] . '">' . $value['title'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label"></label>
                            <div class="col-md-5">
                                <input type="hidden" name="step" id="create_school_step_1">
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
                                <input class="form-control input-sm" type="text" name="school_max_students" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?>">
                            </div>
                        </div>
                        <div class="form-group" >
                            <label class="col-md-2 col-md-offset-2 control-label" for="school_subscription_end"><?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?></label>
                            <div class="col-md-5">
                                <input class="form-control input-sm" id="school_subscription_end" type="text"  name="school_subscription_end" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 col-md-offset-2 control-label"></label>
                            <div class="col-md-5">
                                <input type="hidden" name="step" id="create_school_step_2">
                                <input type="button" name="submit" id="create_school_step_two_button" step="2" 
                                       value="<?php echo TranslationHandler::get_static_text("SCHOOL_FINISH_STEP_TWO"); ?>" class="pull-right btn btn-default btn-sm create_school">     
                            </div>
                        </div>
                    </form>
                </div>

                <div id="step_three" class="hidden">
                    School created.

                    Set school rights - TO DO
                </div>

                <div class="input-container">
                    <div class="col-md-7 center p-t-xl">        
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