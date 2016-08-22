<?php
    require_once 'include/handler/loginHandler.php';

    $loginHandler = new LoginHandler();
?>

<div class="login_overlay">
<div class="login_container">

	<div class="simple-page-wrap">
		<div class="simple-page-logo animated swing">
                    <div id="app-brand">
                        <span id="brand-icon" class="brand-icon">
                            <img src="assets/images/LearnEZ-Maskot-sort-30-30.png" class="fa fa-gg">
                        </span>
                        <span id="app-brand" class="brand-icon foldable text-primary">Learn EZ</span>
                    </div>
		</div><!-- logo -->
		<div class="simple-page-form animated flipInY" id="login-form">
	
	<form method="POST" action="" id="login_form" url="login.php" class="form-horizontal" name="login">
		<div class="form-group">
			<input id="sign-in-email" name="username" class="form-control login_username" placeholder="<?= TranslationHandler::get_static_text("USERNAME");?>" onkeydown = "if (event.keyCode == 13)
                                        document.getElementById('submit_button').click()">
		</div>
		<div class="form-group">
                    <input id="sign-in-password" type="password" name="password" class="form-control login_password" placeholder="<?= TranslationHandler::get_static_text("PASSWORD");?>" onkeydown = "if (event.keyCode == 13)
                                        document.getElementById('submit_button').click()">
		</div>
            
                <input type="hidden" id="token" name="token" class="login_token" value="<?php echo $loginHandler->get_login_token(); ?>">
		<input type="button" name="submit" id="submit_button" class="btn btn-primary submit_login login_submit" value="<?= TranslationHandler::get_static_text("LOGIN");?>">
	</form>
</div><!-- #login-form -->

<div class="simple-page-footer">
	<p><a class="change_page_from_overlay a" page="resetpassword"><?= TranslationHandler::get_static_text("RESET_PASS");?></a> || <a class="change_page_from_overlay a" page="find_certificates"><?= TranslationHandler::get_static_text("FIND_CERTIFICATE");?></a></p>
</div><!-- .simple-page-footer -->


	</div><!-- .simple-page-wrap -->

</div>
</div>