<?php

    switch (isset($_GET['do']) ? $_GET['do'] : null) {
        case 'logout':
            $loginHandler->log_out();
            header('Location: http://'. $_SERVER['HTTP_HOST'] .'/');
            die();
            break;

        default:
            if ($loginHandler->check_login()) {
                header('Location: http://'. $_SERVER['HTTP_HOST'] .'/');
                die();
            }
            if (isset($_POST['submit'])) {
                if ($loginHandler->check_login($_POST['username'], $_POST['password'], $_POST['token'])) {
                    header('Location: http://'. $_SERVER['HTTP_HOST'] .'/');
                    die();
                } else {
                    ErrorHandler::DisplayError($loginHandler->_error->ErrorMessage, false);
                }
            }
    if(isset($_GET['logout'])) {
        if($loginHandler->check_login()) {
            $loginHandler->log_out();
        }
    }
    if(isset($_POST["submit"])) {
        if($loginHandler->check_login($_POST["username"], $_POST["password"], $_POST["token"])) {
            echo "logged in";
        }
    }
    
    if(!$loginHandler->check_login()) {
?>

<div class="text-center">
    <div class="col-xs-12"><h1>Login</h1></div>
    
    <div class="col-xs-12">
        <form method="POST" action="" id="login" name="login">
            <input type="text" id="username" name="username" placeholder="User Name:" class="login_input"><br/>
            <input type="password" id="password" name="password" placeholder="Password:" class="login_input"><br/><br/>
            <input type="hidden" id="token" name="token" value="<?php echo $loginHandler->generate_login_token(); ?>">
            <input type="submit" id="submit" name="submit" value="Login" class="login_submit">
        </form>
    </div>
</div>

<?php
    }
?>
