<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';
    require_once '../../include/handler/pageHandler.php';

    $userHandler = SessionKeyHandler::get_from_session("user_handler", true);
    $pageHandler = SessionKeyHandler::get_from_session("page_handler", true);

    if(isset($_GET['logout'])) {
        if($loginHandler->check_login()) {
            $loginHandler->log_out();
            $rightsHandler->reset_rights();
            $pageHandler->reset();
            $jsonArray['status_value'] = true;
        } else {
            $jsonArray['status_value'] = false;
        }
        echo json_encode($jsonArray);
        die();
    }

    if(isset($_POST)) {
        if($userHandler->edit_user_info($_POST["firstname"], $_POST["surname"], $_POST["email"], $_POST["description"], $_POST["image"])) 
        {
            $pageHandler->reset();
            $jsonArray['status_value'] = true;
        } else {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = $userHandler->error->title;
        }
        echo json_encode($jsonArray);
        die();
    }
?>