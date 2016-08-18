<?php
require_once 'require.php';
require_once '../../include/handler/loginHandler.php';
    $loginHandler = new LoginHandler();
    
    $step = isset($_GET['step']) ? $_GET['step'] : "";
    
    switch($step)
    {
        case "confirmpassword":
            if(!isset($_GET['id']) || !isset($_GET['code']) || !$loginHandler->validate_reset_password($_GET['id'],$_GET['code']))
            {
                ErrorHandler::show_error_page($loginHandler->error);
                die();
            }
            ?>
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="widget main_login">
                            <div class="widget-header">
                                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("RESET_PASS_CONFIRM"); ?></h4>
                            </div>
                            <hr class="widget-separator">
                            <div class="widget-body">
                                <form method="POST" action="" id="reset_pass_submit_code" url="resetpassword.php?step=pass_val" class="form-horizontal" name="reset_pass_submit_email">

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
                                        <input type="button" id="submit_button" name="submit" value="<?php echo TranslationHandler::get_static_text("RESET_PASS_SUBMIT"); ?>" class="center btn btn-default create_submit_info">
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php
        break;
        
        default:
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
                                        <input type="button" id="submit_button" name="submit" value="<?php echo TranslationHandler::get_static_text("INFO_SUBMIT"); ?>" class="center btn btn-default create_submit_info">
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        break;
    }
?>