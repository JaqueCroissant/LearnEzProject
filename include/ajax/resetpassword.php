<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/loginHandler.php';
    require_once '../../include/handler/userHandler.php';

    $loginHandler = SessionKeyHandler::get_from_session("login_handler", true);
    $userHandler = SessionKeyHandler::get_from_session("user_handler", true);

    switch($_GET['step'])
    {
        case "1":
            email_validation($loginHandler);
            break;
        case "2":
            password_validation($loginHandler, $userHandler);
            break;
    }

    function password_validation($loginHandler, $userHandler)
    {
        if(isset($_GET['id']) && isset($_GET['code']))
        {
            if(!$loginHandler->validate_reset_password($_GET['id'],$_GET['code']))
            {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $userHandler->error->title;
            }
            elseif(isset($_POST['submit'])) 
            {
                if($userHandler->change_password($_GET['id'],$_GET['code'],$_POST['password'],$_POST['password_confirm']))
                {
                    SessionKeyHandler::add_to_session("user_handler", $userHandler, true);
                    $jsonArray['status_value'] = true;
                }
                else
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $userHandler->error->title;
                }
            }
            
        }
        else
        {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = $userHandler->error->title;
        }
        echo json_encode($jsonArray);
        die();
    }

    
    function email_validation($loginHandler)
    {
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
    }
?>