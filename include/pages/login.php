<?php
    
    if(isset($_GET['logout'])) {
        if($loginHandler->check_login()) {
            $loginHandler->log_out();
            $rightsHandler->reset_rights();
            $pageHandler->reset();
            //echo '<META HTTP-EQUIV="Refresh" Content="0; URL=index.php?page=front">';
        }
    }
    
    if(isset($_POST["submit"])) {
        if($loginHandler->check_login($_POST["username"], $_POST["password"], $_POST["token"])) {
            TranslationHandler::reset_language();
            $pageHandler->reset();
            //echo '<META HTTP-EQUIV="Refresh" Content="0; URL=index.php?page=login">';
        } else {
            echo $loginHandler->error->title;
        }
    }
    
    if(!$loginHandler->check_login() || isset($_GET["logout"])) {
        if(!$rightsHandler->right_exists("CHANGE_PASSWORD"))
        {
            echo "Jeg har ikke rettigheder til at ændre mit kodeord<br/>";
        }
?>

<div class="text-center">
    <div class="col-xs-12"><h1>Login</h1></div>
    
    <div class="col-xs-12">
        <form method="POST" action="" id="login" name="login">
            <input type="text" id="username" name="username" placeholder="User Name:" class="form-control login_input"><br/>
            <input type="password" id="password" name="password" placeholder="Password:" class="form-control login_input"><br/><br/>
            <input type="hidden" id="token" name="token" value="<?php echo $loginHandler->generate_login_token(); ?>">
            <input type="submit" id="submit" name="submit" value="Login" class="form-control login_submit">
        </form>
    </div>
</div>

<br />
    <a href="index.php?page=resetpassword">Reset password</a>

<?php
    } else { 
        if(!$rightsHandler->right_exists("CHANGE_PASSWORD"))
        {
            echo "Jeg har ikke rettigheder til at ændre mit kodeord<br/>";
        }
        
        echo "Username: " . SessionKeyHandler::get_from_session("user", true)->username;
        echo "Email: " . SessionKeyHandler::get_from_session("user", true)->email;
        echo "Firstname: " . SessionKeyHandler::get_from_session("user", true)->firstname;
        echo "Surname: " . SessionKeyHandler::get_from_session("user", true)->surname;
        echo "Last login: " . SessionKeyHandler::get_from_session("user", true)->last_login;
        echo "User type: " . SessionKeyHandler::get_from_session("user", true)->user_type_id;
        echo "Language: " . SessionKeyHandler::get_from_session("user", true)->language_id;
        $not = new NotificationHandler();
?>
<br/>
<br/>
<?php
    echo $not->get_number_of_unread(SessionKeyHandler::get_from_session("user", true)->id);
    echo "<br/>";
?>
    <br />
    <a href="index.php?page=login&logout=true">Logout</a>

<?php
    }
?>
