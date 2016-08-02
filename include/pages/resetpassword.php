<?php
require_once 'require.php';

    if(isset($_GET['id']) && isset($_GET['code']) && $loginHandler->validate_reset_password($_GET['id'],$_GET['code']))
    {
        ?>

            <div class="text-center">
                <div class="col-xs-12"><?php echo TranslationHandler::get_static_text("RESET_PASS_CONFIRM"); ?></div>
    
                <div class="col-xs-12">
                    <form method="POST" action="" id="reset_pass_submit_code" name="reset_pass_submit_code">
                        <input type="password" id="password" name="password" placeholder="<?php echo TranslationHandler::get_static_text("PASSWORD"); ?>" class="login_input"><br/>
                        <input type="password" id="password_confirm" name="password_confirm" placeholder="<?php echo TranslationHandler::get_static_text("CONFIRM_PASSWORD"); ?>" class="login_input"><br/>
                        <input type="button" id="submit" name="submit" value="<?php echo TranslationHandler::get_static_text("RESET_PASS_SUBMIT"); ?>" class="login_submit">
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 ">
                    <div class="widget main_login">
                        <div class="widget-header">
                            <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("RESET_PASS_CONFIRM"); ?></h4>
                        </div>
                        <hr class="widget-separator">
                        <div class="widget-body">
                            <form method="POST" action="" id="reset_pass_submit_email" url="resetpassword.php?step=mail_val" class="form-horizontal" name="reset_pass_submit_email">

                                <div class="form-group">
                                    <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("PASSWORD"); ?></label>
                                    <div class="col-sm-4">
                                        <input class="form-control input-sm reset_pass_email" type="password" name="email" placeholder="<?php echo TranslationHandler::get_static_text("PASSWORD"); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("CONFIRM_PASSWORD"); ?></label>
                                    <div class="col-sm-4">
                                        <input class="form-control input-sm reset_pass_email" type="password" name="email" placeholder="<?php echo TranslationHandler::get_static_text("CONFIRM_PASSWORD"); ?>">
                                    </div>
                                </div>
                                <div class="form-group center">
                                    <input type="button" id="submit_button" name="submit" value="<?php echo TranslationHandler::get_static_text("RESET_PASS_SUBMIT"); ?>" class="center btn btn-default reset_pass_submit_email2">
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php
    }
    else
    {
    ?>
        <div class="row">
            <div class="col-md-12 ">
                <div class="widget main_login">
                    <div class="widget-header">
                        <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("RESET_PASS"); ?></h4>
                    </div>
                    <hr class="widget-separator">
                    <div class="widget-body">
                        <form method="POST" action="" id="reset_pass_submit_email" url="resetpassword.php?step=mail_val" class="form-horizontal" name="reset_pass_submit_email">

                            <div class="form-group">
                                <label class="col-sm-2 col-sm-offset-2 control-label" for="email"><?php echo TranslationHandler::get_static_text("RESET_PASS_ENTER_MAIL"); ?></label>
                                <div class="col-sm-4">
                                    <input class="form-control input-sm reset_pass_email" type="text" name="email" placeholder="<?php echo TranslationHandler::get_static_text("INFO_EMAIL"); ?>">
                                </div>
                            </div>
                            <div class="form-group center">
                                <input type="button" id="submit_button" name="submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="center btn btn-default reset_pass_submit_email2">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

?>