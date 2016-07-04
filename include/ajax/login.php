<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/loginHandler.php';
require_once '../../include/handler/pageHandler.php';
require_once '../../include/handler/rightsHandler.php';


$loginHandler = SessionKeyHandler::get_from_session("login_handler", true);
$pageHandler = SessionKeyHandler::get_from_session("page_handler", true);
$rightsHandler = SessionKeyHandler::get_from_session("rights_handler", true);

if(isset($_GET['logout'])) {
    if($loginHandler->check_login()) {
        $loginHandler->log_out();
        $rightsHandler->reset_rights();
        $pageHandler->reset();
        SessionKeyHandler::add_to_session("page_handler", $pageHandler, true);
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
    }
    echo json_encode($jsonArray);
    die();
}

if(isset($_POST)) {
    if($loginHandler->check_login($_POST["username"], $_POST["password"], $_POST["token"])) {
        TranslationHandler::reset_language();
        $pageHandler->reset();
        SessionKeyHandler::add_to_session("page_handler", $pageHandler, true);
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $loginHandler->error->title;
    }
    echo json_encode($jsonArray);
    die();
}
    