<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';

    $userHandler = SessionKeyHandler::get_from_session("user_handler", true);

    switch($_GET['step'])
    {
        case '1':
            if(isset($_POST))
            {
                if($userHandler->create_new_profile($_POST["firstname"], $_POST["surname"], $_POST["email"],
                $_POST["password"], $_POST["usertype"], $_POST["school_id"], $_POST["class_ids"]))
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
        break;

        case '2':
            if(isset($_POST))
            {
                if($userHandler->import_users($_POST["csv_file"], $_POST["school_id"]))
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
        break;
    }
?>