<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/loginHandler.php';
$loginHandler = SessionKeyHandler::get_from_session("login_handler", true);
?>

<div class="text-center">
    <div class="col-xs-12"><h1>Login</h1></div>
    
    <div class="col-xs-12">
        <form method="POST" action="" id="login_form" url="login.php" name="login">
            <input type="text" id="username" name="username" placeholder="User Name:" class="form-control login_input"><br/>
            <input type="password" id="password" name="password" placeholder="Password:" class="form-control login_input"><br/><br/>
            <input type="hidden" id="token" name="token" value="<?php echo $loginHandler->get_login_token(); ?>">
            <input type="button" id="submit_button" name="submit" value="Login" class="submit_login form-control login_submit">
        </form>
    </div>
</div>

<br />
<a class="change_page" page="resetpassword" id="resetpassword" href="#">Reset password</a>
