<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/loginHandler.php';
require_once '../../include/handler/pageHandler.php';
require_once '../../include/handler/rightsHandler.php';
require_once '../../include/handler/settingsHandler.php';
require_once '../../include/handler/userHandler.php';

$loginHandler = new LoginHandler();
$settingsHandler = new SettingsHandler();
$userHandler = new UserHandler();

if(isset($_GET['logout'])) {


    if($loginHandler->check_login()) {
        $loginHandler->log_out();
        TranslationHandler::reset();
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
    }

    echo json_encode($jsonArray);
    die();

}

if(isset($_GET["init"]))
{
            $language_id = isset($_POST['new_language']) ? $_POST['new_language'] : "";
            $os_id = isset($_POST['new_os']) ? $_POST['new_os'] : "";
            $email = isset($_POST['new_email']) ? $_POST['new_email'] : "";
            $password = isset($_POST['new_password']) ? $_POST['new_password'] : "";
            $password_copy = isset($_POST['new_password_confirm']) ? $_POST['new_password_confirm'] : "";

            if($settingsHandler->initial_update($language_id, $os_id))
            {
                if($userHandler->init_user_info($email, $password, $password_copy))
                {
                    $username = isset($_POST["username"]) ? $_POST["username"] : null;
                    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : null;
                    $old_password = isset($_POST["password"]) ? $_POST["password"] : null;
                    $token = isset($_POST["token"]) ? $_POST["token"] : null;

                    $password_to_use = !empty($new_password) ? $new_password : $old_password;

                    if($loginHandler->check_login($username, $password_to_use, $token))
                    {
                        $jsonArray['status_value'] = true;
                        TranslationHandler::reset();
                    }
                    else
                    {
                        $jsonArray['status_value'] = false;
                        $jsonArray['error'] = $loginHandler->error->title;
                    }
                }
                else
                {
                    $jsonArray['error'] = $userHandler->error->title;
                    $jsonArray['status_value'] = false;
                }
            }
            else
            {
                $jsonArray['error'] = $settingsHandler->error->title;
                $jsonArray['status_value'] = false;
            }

            echo json_encode($jsonArray);
            die();
}

if(isset($_POST)) {
    $username = isset($_POST["username"]) ? $_POST["username"] : null;
    $password = isset($_POST["password"]) ? $_POST["password"] : null;
    $token = isset($_POST["token"]) ? $_POST["token"] : null;
    if($loginHandler->check_login($username, $password, $token)) {

        $jsonArray['status_value'] = true;
        TranslationHandler::reset();
    } else {

        $jsonArray['status_value'] = false;

        if($loginHandler->error->code == "ACTIVATE")
        {
            $jsonArray['user_setup'] = SessionKeyHandler::session_exists("user_setup");
        }
        else
        {
            $jsonArray['error'] = $loginHandler->error->title;
        }
    }
    echo json_encode($jsonArray);
    die();
}
    