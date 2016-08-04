<?php
require_once 'require.php';
require_once '../../include/handler/loginHandler.php';
require_once '../../include/handler/courseHandler.php';

$loginHandler = new LoginHandler();
?>

<div class="row">
    <div class="col-md-12 ">
        <div class="widget main_login">
            <div class="widget-header">
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("LOGIN"); ?></h4>
            </div>
            <hr class="widget-separator">
            <div class="widget-body">
                <form method="POST" action="" id="login_form" url="login.php" class="form-horizontal" name="login">
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label" for="username"><?php echo TranslationHandler::get_static_text("USERNAME"); ?></label>
                        <div class="col-sm-5">
                            <input class="form-control input-sm login_username" type="text" name="username" placeholder="<?php echo TranslationHandler::get_static_text("USERNAME"); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label" for="password"><?php echo TranslationHandler::get_static_text("PASSWORD"); ?></label>
                        <div class="col-sm-5">
                            <input class="form-control input-sm login_password" type="password" name="password" onkeydown = "if (event.keyCode == 13)
                                        document.getElementById('submit_button').click()"
                                   placeholder="<?php echo TranslationHandler::get_static_text("PASSWORD"); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label"></label>
                        <div class="col-sm-5">
                            <input type="hidden" id="token" name="token" class="login_token" value="<?php echo $loginHandler->get_login_token(); ?>">
                            <input type="button" id="submit_button" name="submit" 
                                   value="<?php echo TranslationHandler::get_static_text("LOGIN"); ?>" class="pull-right btn btn-default btn-sm submit_login login_submit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="widget first_time_login" style="opacity:0;height:0 !important;">
            <div class="widget-header">
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("ACCOUNT_ACTIVATION"); ?></h4>
            </div>
            <hr class="widget-separator">
            <div class="widget-body">
                <div class="col-sm-6">
                    <label class="control-label activation_greeting"><?php echo TranslationHandler::get_static_text("HELLO") . " "; ?></label>
                    <p><?php echo nl2br(TranslationHandler::get_static_text("ACCOUNT_ACTIVATION_INFO")); ?></p>
                </div>
                <div class="col-sm-6">
                    <form method="POST" action="" id="activation_form" url="login.php?init" class="" name="login">
                        <div class="form-group">
                            <label class="control-label" for="new_email"><?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?></label>
                            <input class="form-control activation_email" type="text" name="new_email" onkeydown = "if (event.keyCode == 13)
                                        document.getElementById('submit_button').click()"
                                   placeholder="<?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="new_os"><?php echo TranslationHandler::get_static_text("OS"); ?></label>
                            <select name="new_os" class="form-control" id="os_select" data-plugin="select2" data-options="{minimumResultsForSearch: Infinity}">
                                <?php
                                foreach (courseHandler::get_os_options() as $option) {
                                    echo '<option value="' . $option["id"] . '">' . $option["title"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="new_language"><?php echo TranslationHandler::get_static_text("LANGUAGE"); ?></label>
                            <select name="new_language" class="form-control" id="language_select" data-plugin="select2" data-options="{minimumResultsForSearch: 5}">
                                <?php
                                foreach (TranslationHandler::get_language_options() as $lang) {
                                    echo '<option value="' . $lang["id"] . '">' . $lang["title"] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="accordion" id="accordion" role="tablist" aria-multiselectable="false">
                            <div class="panel-default">
                                <div class="panel-heading" style="border:none;" role="tab" id="heading-1">
                                    <a class="accordion-toggle new_password_change" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-1" aria-expanded="false" aria-controls="collapse-1">
                                        <label for="textarea1"><?php echo TranslationHandler::get_static_text("NEW_PASSWORD"); ?></label>
                                        <i class="fa acc-switch"></i>
                                    </a>
                                </div>
                                <div id="collapse-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-1" aria-expanded="false">
                                    <div class="form-group">
                                        <label class="control-label" for="new_password"><?php echo TranslationHandler::get_static_text("PASSWORD"); ?></label>
                                        <input class="form-control" type="password" name="new_password" onkeydown = "if (event.keyCode == 13)
                                                    document.getElementById('submit_button').click()"
                                               placeholder="<?php echo TranslationHandler::get_static_text("PASSWORD"); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="new_password_confirm"><?php echo TranslationHandler::get_static_text("CONFIRM_PASSWORD"); ?></label>
                                        <input class="form-control" type="password" name="new_password_confirm" onkeydown = "if (event.keyCode == 13)
                                                    document.getElementById('submit_button').click()"
                                               placeholder="<?php echo TranslationHandler::get_static_text("CONFIRM_PASSWORD"); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="pull-right">
                                <input type="hidden" id="token" name="token" class="activation_token" value="<?php echo $loginHandler->get_login_token(); ?>">
                                <input type="hidden" id="username_id" name="username" value="" class="activation_username">
                                <input type="hidden" id="password_id" name="password" value="" class="activation_password">
                                <input type="button" id="submit_button" name="submit" 
                                       value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="pull-right btn btn-default btn-sm submit_login login_submit">
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div> 
<script src="assets/js/include_app.js" type="text/javascript"></script>
