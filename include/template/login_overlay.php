<div class="login_container">
	<div id="back-to-home">
		<a href="index.html" class="btn btn-outline btn-default"><i class="fa fa-home animated zoomIn"></i></a>
	</div>
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
			<input id="sign-in-email" type="email" class="form-control" placeholder="<?= TranslationHandler::get_static_text("USERNAME");?>">
		</div>
		<div class="form-group">
			<input id="sign-in-password" type="password" class="form-control" placeholder="<?= TranslationHandler::get_static_text("PASSWORD");?>">
		</div>
		<input type="submit" class="btn btn-primary" value="<?= TranslationHandler::get_static_text("LOGIN");?>">
	</form>
</div><!-- #login-form -->

<div class="simple-page-footer">
	<p><a class="change_page a" page="resetpassword"><?= TranslationHandler::get_static_text("RESET_PASS");?></a> || <a class="change_page a" page="find_certificates"><?= TranslationHandler::get_static_text("FIND_CERTIFICATE");?></a></p>
</div><!-- .simple-page-footer -->


	</div><!-- .simple-page-wrap -->

</div>