<?php
require_once 'include/handler/loginHandler.php';
$display = $loginHandler->check_login() || (isset($_GET["page"]) && $_GET["page"] == "resetpassword" && isset($_GET["step"]) && $_GET["step"] == "confirmpassword");
$loginHandler = new LoginHandler();
?>

<div class="login_overlay" style="<?= $display ? 'display:none;' : '' ?>">
     <div class="login_container">

     <div class="simple-page-wrap">
        <div class="simple-page-logo animated swing">
            <div id="app-brand">
                <span id="brand-icon" class="brand-icon">
                    <img src="assets/images/logo-blue.png" class="fa fa-gg">
                </span>
            </div>
        </div>
        <div class="simple-page-form animated flipInY" id="login-form" style="background-color: #0074af !important;">
            <h4 class="form-title m-b-xl text-center text-white"><?= TranslationHandler::get_static_text("LOGIN_WITH_YOUR_LEARNEZ_ACCOUNT"); ?></h4>
            <form method="POST" action="" id="login_form" url="login.php" class="form-horizontal" name="login">
                <div class="form-group" style="margin-left:0px !important;margin-right:0px !important;">
                    <input id="sign-in-email" name="username" class="form-control login_username login_input_text" placeholder="<?= TranslationHandler::get_static_text("USERNAME"); ?>" onkeydown = "if (event.keyCode == 13)
                                    $('.catch_login').click()" style="border-bottom: 1px solid #fff;background-color: #0074af !important;margin-left:0px !important;margin-right:0px !important;color: #FFF !important;">
                </div>
                <div class="form-group" style="margin-left:0px !important;margin-right:0px !important;">
                    <input id="sign-in-password" type="password" name="password" class="form-control login_password login_input_text" placeholder="<?= TranslationHandler::get_static_text("PASSWORD"); ?>" onkeydown = "if (event.keyCode == 13)
                                $('.catch_login').click()" style="border-bottom: 1px solid #fff;background-color: #0074af !important;color: #FFF !important;">
                </div>

                <input type="hidden" id="token" name="token" class="login_token" value="<?php echo $loginHandler->get_login_token(); ?>">
                <input type="button" name="submit" id="submit_button" class="btn btn-primary submit_login login_submit catch_login" value="<?= TranslationHandler::get_static_text("LOGIN"); ?>" style="border-bottom: 1px solid #fff;background-color: #FFF !important;color: #0074af !important;">
            </form>
        </div>

        <div class="simple-page-footer">
            <p><a class="change_page_from_overlay a text-primary m-r-md" page="resetpassword"><?= TranslationHandler::get_static_text("RESET_PASS"); ?></a> || <a class="change_page_from_overlay a text-primary m-l-md" page="find_certificates"><?= TranslationHandler::get_static_text("FIND_CERTIFICATE"); ?></a></p>
        </div>


    </div>

</div>
</div>