<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/loginHandler.php';
    require_once '../../include/handler/userHandler.php';

    $loginHandler = SessionKeyHandler::get_from_session("login_handler", true);
    $userHandler = SessionKeyHandler::get_from_session("user_handler", true);

    switch($_GET['step'])
    {
        //VERIFICER BRUGERINFO
        case "1":
            if(isset($_POST)) 
            {
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
        break;
        
        //VERIFICER EMAIL
        case "2":
            if(isset($_POST))
            {
                if(!$loginHandler->reset_password($_POST['email'])) 
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $loginHandler->error->title;
                } 
                else 
                {
                    $jsonArray['status_value'] = true;
                }
                echo json_encode($jsonArray);
            }
        break;
    }
?>