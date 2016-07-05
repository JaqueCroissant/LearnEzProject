<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';

    $userHandler = SessionKeyHandler::get_from_session("user_handler", true);

    if(isset($_POST)) {
        if($userHandler->edit_user_info($_POST["firstname"], $_POST["surname"], $_POST["email"], $_POST["description"])) 
        {
            SessionKeyHandler::add_to_session("user", $userHandler->_user, true);
            SessionKeyHandler::add_to_session("user_handler", $userHandler, true);

            $jsonArray['status_value'] = true;
        } 
        else 
        {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = $userHandler->error->title;
        }
        echo json_encode($jsonArray);
        die();
    }
?>