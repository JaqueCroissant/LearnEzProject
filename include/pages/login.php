<?php
require_once 'require.php';
require_once '../../include/handler/loginHandler.php';
$loginHandler = new LoginHandler();
?>

<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="widget-header">
                <h4 class="widget-title"><?php echo TranslationHandler::get_static_text("LOGIN"); ?></h4>
            </div>
            <hr class="widget-separator">
            <div class="widget-body">
                <form method="POST" action="" id="login_form" url="login.php" class="form-horizontal" name="login">
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label" for="username"><?php echo TranslationHandler::get_static_text("USERNAME"); ?></label>
                        <div class="col-md-5">
                            <input class="form-control input-sm" type="text" name="username" placeholder="<?php echo TranslationHandler::get_static_text("USERNAME"); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label" for="password"><?php echo TranslationHandler::get_static_text("PASSWORD"); ?></label>
                        <div class="col-md-5">
                            <input class="form-control input-sm" type="password" name="password" onkeydown = "if (event.keyCode == 13) document.getElementById('submit_button').click()"
                            placeholder="<?php echo TranslationHandler::get_static_text("PASSWORD"); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-sm-offset-2 control-label"></label>
                        <div class="col-md-5">
                            <input type="hidden" id="token" name="token" value="<?php echo $loginHandler->get_login_token(); ?>">
                            <input type="button" id="submit_button" name="submit" 
                                   value="<?php echo TranslationHandler::get_static_text("LOGIN"); ?>" class="pull-right btn btn-default btn-sm submit_login login_submit">  
                            <a class="change_page pull-right p-r-lg p-t-xs" page="resetpassword" id="resetpassword" href="#">Reset password</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>