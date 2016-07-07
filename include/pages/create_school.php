<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/schoolHandler.php';
$schoolHandler = SessionKeyHandler::get_from_session("school_handler", true);
$schoolHandler->get_school_types();
?>

<div class="col-md-12">
    <div class="widget">
        <header class="widget-header">
            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("SCHOOL_CREATE_NEW"); ?></h4>
        </header>
        <hr class="widget-separator">
        <div class="widget-body">
            <form method="post" id="create_school_step_one" action="" class="form-horizontal" url="create_school.php">
                <div class="form-group">
                    <label class="col-sm-2 col-sm-offset-2 control-label" for="school_name"><?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?></label>
                    <div class="col-md-5">
                        <input class="form-control input-sm" type="text" name="school_name" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_NAME"); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-sm-offset-2 control-label" for="school_address"><?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?></label>
                    <div class="col-md-5">
                        <input class="form-control input-sm" type="text" name="school_address" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_ADDRESS"); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-sm-offset-2 control-label" for="school_phone"><?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?></label>
                    <div class="col-md-5">
                        <input class="form-control input-sm" type="text" name="school_phone" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_PHONE"); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-sm-offset-2 control-label" for="school_email"><?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?></label>
                    <div class="col-md-5">
                        <input class="form-control input-sm" type="text" name="school_email" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_EMAIL"); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-sm-offset-2 control-label" for="school_type_id"><?php echo TranslationHandler::get_static_text("SCHOOL_TYPE"); ?></label>
                    <div class="col-md-5">
                        <select class="form-control input-sm">
                            <?php
                            foreach ($schoolHandler->school_types as $value) {
                                echo '<option value="' . $value['id'] . '">' . $value['title'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 col-sm-offset-2 control-label"></label>
                    <div class="col-md-5">
                        <input type="hidden" name="step" id="create_school_step">
                        <input type="button" name="submit" id="create_school_step_one_button" step="1" 
                               value="<?php echo TranslationHandler::get_static_text("SCHOOL_FINISH_STEP_ONE"); ?>" class="pull-right btn btn-default btn-sm create_school">   
                    </div>
            </form>
        </div>

        <div id="step_two" class="hidden">
            <div class="material_design_header"><?php echo TranslationHandler::get_static_text("SCHOOL_CREATE_NEW"); ?></div>
            <form method="POST" id="create_school_step_two" action="" url="create_school.php">
                <table style="width:100%">
                    <tr>
                        <td class="left-col">
                            <?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?>
                        </td>
                        <td class="right-col">
                            <input class="material_design_input" name="school_max_students" placeholder="<?php echo TranslationHandler::get_static_text("SCHOOL_MAX_STUDENTS"); ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="left-col">
                            <?php echo TranslationHandler::get_static_text("SCHOOL_SUBSCRIPTION_END"); ?>
                        </td>
                        <td class="right-col">
                            <input name="school_subscription_end" class="material_design_input" type="text" id="datepicker">
                        </td>
                    </tr>
                    <tr>
                        <td class="left-col">
                        </td>
                        <td class="right-col">
                            <input type="hidden" name="step" id="create_school_step_2">
                            <input type="button" name="submit" id="create_school_step_two_button" step="2" value="<?php echo TranslationHandler::get_static_text("SCHOOL_FINISH_STEP_TWO"); ?>" class="pull-right btn btn-default btn-sm create_school">   
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div id="step_three" class="hidden">

        </div>

        <div class="input-container">
            <div class="col-md-7 center p-t-xl">        
                <div class="progress">
                    <div class="progress-bar" style="width: 33.33%;"><span>Step 1</span></div>
                    <div class="progress-bar-inactive" style="width: 33.33%;"><span>Step 2</span></div>
                    <div class="progress-bar-inactive" style="width: 33.33%; float: left;"><span>Step 3</span></div>
                </div>
            </div>
        </div>
    </div>

</div>