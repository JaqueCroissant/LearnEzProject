<?php
    if(!$loginHandler->reset_password("mathiasklinke@gmail.com")) {
        echo $loginHandler->error->title;
    }
    if(isset($_GET['logout'])) {
        if($loginHandler->check_login()) {
            $loginHandler->log_out();
        }
    }
    
    if(isset($_POST["submit"])) {
        if($loginHandler->check_login($_POST["username"], $_POST["password"], $_POST["token"])) {
            header("Location: index.php?page=front");
        } else {
            echo $loginHandler->error->title;
        }
    }
    
    if(!$loginHandler->check_login() || isset($_GET["logout"])) {
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
    } else { 
    
    echo "Username: " . SessionKeyHandler::GetFromSession("user", true)->username;
    echo "Email: " . SessionKeyHandler::GetFromSession("user", true)->email;
    echo "Firstname: " . SessionKeyHandler::GetFromSession("user", true)->firstname;
    echo "Surname: " . SessionKeyHandler::GetFromSession("user", true)->surname;
    echo "Last login: " . SessionKeyHandler::GetFromSession("user", true)->last_login;
    echo "User type: " . SessionKeyHandler::GetFromSession("user", true)->user_type_id;
    echo "Language: " . SessionKeyHandler::GetFromSession("user", true)->language_id;
    $not = new NotificationHandler();
?>
<br/>
<br/>
<?php
    echo $not->getNumberOfUnread(SessionKeyHandler::GetFromSession("user", true)->id);
    echo "<br/>";
    $notifications = $not->getNotifications(SessionKeyHandler::GetFromSession("user", true)->id, 2);
    foreach ($notifications as $value) {
        echo $value->title . "<br/>";
    }
?>
    <br />
    <a href="index.php?page=login&logout=true">Logout</a>

<?php
    }
?>
