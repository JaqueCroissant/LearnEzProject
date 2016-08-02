<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/loginHandler.php';
    require_once '../../include/handler/userHandler.php';

    $loginHandler = new LoginHandler();
    $userHandler = new UserHandler();

    switch($_GET['step'])
    {
        case "mail_val":
            email_validation($loginHandler);
            break;
        case "pass_val":
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