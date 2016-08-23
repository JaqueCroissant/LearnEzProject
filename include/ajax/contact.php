<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/contactHandler.php';

    $contactHandler = new ContactHandler();

    switch($_GET['step'])
    {
        case 'login':
            if(isset($_POST) && $contactHandler->is_logged_in())
            {
                $name = $contactHandler->_user->firstname . " " . $contactHandler->_user->surname;
                $email = $contactHandler->_user->email;
                $context = isset($_POST["context"]) ? $_POST["context"] : "";
                $subject = isset($_POST["subject"]) ? $_POST["subject"] : "";
                $message = isset($_POST["message"]) ? $_POST["message"] : "";
                
                if(!$contactHandler->generate_support_mail($name, $email, $context, $subject, $message))
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $contactHandler->error->title;
                }
                else
                {
                    $jsonArray['status_value'] = true;
                    $jsonArray['succes'] = "JAAAAAA";
                }
                
                echo json_encode($jsonArray);
                die();
            }
        break;

        case 'nologin':
            if(isset($_POST) && !$contactHandler->is_logged_in())
            {
                $name = isset($_POST["name"]) ? $_POST["name"] : "";
                $email = isset($_POST["email"]) ? $_POST["email"] : "";
                $context = isset($_POST["context"]) ? $_POST["context"] : "";
                $subject = isset($_POST["subject"]) ? $_POST["subject"] : "";
                $message = isset($_POST["message"]) ? $_POST["message"] : "";
                
                if(!$contactHandler->generate_support_mail($name, $email, $context, $subject, $message))
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $contactHandler->error->title;
                }
                else
                {
                    $jsonArray['status_value'] = true;
                    $jsonArray['succes'] = "JAAAAAA 2";
                }
                
                echo json_encode($jsonArray);
                die();
            }

        break;

        default:
            ErrorHandler::show_error_page("DEFAULT");
    }
?>