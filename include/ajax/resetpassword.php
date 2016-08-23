<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/loginHandler.php';
    require_once '../../include/handler/userHandler.php';
    require_once '../../include/handler/contactHandler.php';

    $loginHandler = new LoginHandler();
    $userHandler = new UserHandler();
    $contactHandler = new ContactHandler();

    switch($_GET['step'])
    {
        case "mail_val":
            email_validation($loginHandler, $contactHandler);
            break;
        case "pass_val":
            password_validation($loginHandler, $userHandler);
            break;
        default:
            die();
            break;
    }

    function password_validation($loginHandler, $userHandler)
    {
        if(isset($_POST['id']) && isset($_POST['code']))
        {
            if(!$loginHandler->validate_reset_password($_POST['id'],$_POST['code']))
            {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $loginHandler->error->title;
            }
            else
            {
                if($userHandler->change_password($_POST['id'],$_POST['code'],$_POST['password'],$_POST['password_confirm']))
                {
                    $jsonArray['success'] = TranslationHandler::get_static_text("PASS_HAS_BEEN_RESET");
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

    
    function email_validation($loginHandler, $contactHandler)
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
                if($contactHandler->generate_reset_mail($loginHandler->reset_id,$loginHandler->reset_email, $loginHandler->reset_validation))
                {
                    $jsonArray['success'] = TranslationHandler::get_static_text("PASS_RESET_MAIL_SENT");
                    $jsonArray['status_value'] = true;
                }
                else
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $contactHandler->error->title;
                }
                
            }
            echo json_encode($jsonArray);
            die();
        }
    }
?>