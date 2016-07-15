<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/loginHandler.php';
require_once '../../include/handler/pageHandler.php';
require_once '../../include/handler/rightsHandler.php';

$loginHandler = new LoginHandler();

if(isset($_GET['logout'])) {
    if($loginHandler->check_login()) {
        $loginHandler->log_out();
        RightsHandler::reset_rights();
        $jsonArray['status_value'] = true;
    } else {
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
        TranslationHandler::reset();
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $loginHandler->error->title;
    }
    echo json_encode($jsonArray);
    die();
}
    