<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/userHandler.php';
require_once '../../include/handler/schoolHandler.php';
require_once '../../include/handler/contactHandler.php';

$userHandler = new UserHandler();
$schoolHandler = new SchoolHandler();
$contactHandler = new ContactHandler();

if(isset($_POST))
{
    $current_step = isset($_GET["step"]) ? $_GET["step"] : null;
    
    switch($current_step)
    {
        case 'update':
            
            $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : "";
            $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : "";
            $surname = isset($_POST['surname']) ? $_POST['surname'] : "";
            $username = isset($_POST['username']) ? $_POST['username'] : "";
            $email = isset($_POST['email']) ? $_POST['email'] : "";
            $description = isset($_POST['description']) ? $_POST['description'] : "";
            $password = isset($_POST['password']) ? $_POST['password'] : "";
            $school_id = isset($_POST['school_id']) ? $_POST['school_id'] : "";
            $class_ids = isset($_POST['class_name']) ? $_POST['class_name'] : "";
            $type_id = isset($_POST['type_id']) ? $_POST['type_id'] : "";
            
            
            
            if($type_id > 2 && $schoolHandler->school_has_classes($school_id, $class_ids))
            {
                if($userHandler->edit_account($user_id, $first_name, $surname, $email, $description, $password, $class_ids))
                {
                    if(empty($password))
                    {
                        $jsonArray['success'] = TranslationHandler::get_static_text("ACCOUNT_UPDATED");
                        $jsonArray['status_value'] = true;
                    }
                    else
                    {
                        $user_object = new User();
                        $user_object->firstname = $first_name;
                        $user_object->username = $username;
                        $user_object->email = $email;
                        $user_object->unhashed_password = $password;
                        $user_array[] = $user_object;
                        
                        if($contactHandler->distribute_credentials($user_array))
                        {
                            $jsonArray['success'] = TranslationHandler::get_static_text("ACCOUNT_UPDATED");
                            $jsonArray['status_value'] = true;
                        }
                        else
                        {
                            $jsonArray['error'] = $userHandler->error->title;
                            $jsonArray['status_value'] = false;
                        }
                    }
                }
                else
                {
                    $jsonArray['error'] = $userHandler->error->title;
                    $jsonArray['status_value'] = false;
                }
            }
            else if($type_id <= 2)
            {
                if($userHandler->edit_account($user_id, $first_name, $surname, $email, $description, $password))
                {
                    $jsonArray['success'] = TranslationHandler::get_static_text("ACCOUNT_UPDATED");
                    $jsonArray['status_value'] = true;
                }
                else
                {
                    $jsonArray['error'] = $userHandler->error->title;
                    $jsonArray['status_value'] = false;
                }
            }
            else
            {
                $jsonArray['error'] = $schoolHandler->error->title;
                $jsonArray['status_value'] = false;
            }
            
            echo json_encode($jsonArray);
            die();
            
        break;
        
        case 'generate_password':
            
            $jsonArray['password'] = $userHandler->random_char(8);
            $jsonArray['status_value'] = true;
            
            echo json_encode($jsonArray);
            die();
        break;

        case 'generate_and_insert_password':

            $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : array();

            if($userHandler->assign_passwords($user_id))
            {
                if($contactHandler->distribute_credentials($userHandler->temp_user_array))
                {
                    $jsonArray['password'] = $userHandler->temp_user_array[$user_id[0]]->unhashed_password;
                    $userHandler->temp_user_array = array();
                    $jsonArray['success'] = TranslationHandler::get_static_text("ACCOUNT_PASS_ASSIGNED");
                    $jsonArray['status_value'] = true;
                }
                else
                {
                    $userHandler->temp_user_array = array();
                    $jsonArray['error'] = $contactHandler->error->title;
                    $jsonArray['status_value'] = false;
                }
            }
            else
            {
                $jsonArray['error'] = $userHandler->error->title;
                $jsonArray['status_value'] = false;
            }

            echo json_encode($jsonArray);
            die();
        break;

        case 'set_availability':

            $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : "";
            
            if($userHandler->set_user_availability($user_id))
            {
                $jsonArray['success'] = TranslationHandler::get_static_text("ACCOUNT_UPDATED");
                $jsonArray['status_value'] = true;
            }
            else
            {
                $jsonArray['error'] = $userHandler->error->title;
                $jsonArray['status_value'] = false;
            }

            echo json_encode($jsonArray);
            die();
        break;

        case 'delete_acc':

            $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : "";

            if($userHandler->delete_user($user_id))
            {
                $jsonArray['success'] = TranslationHandler::get_static_text("ACCOUNT_DELETED");
                $jsonArray['status_value'] = true;
            }
            else
            {
                $jsonArray['error'] = $userHandler->error->title;
                $jsonArray['status_value'] = false;
            }

            echo json_encode($jsonArray);
            die();
        break;

        case 'assign_passwords':

            $user_ids = isset($_POST['user_ids']) ? $_POST['user_ids'] : array();

            if($userHandler->assign_passwords($user_ids))
            {
                SessionKeyHandler::add_to_session("new_passwords", $userHandler->temp_user_array, true);
                $jsonArray['host'] = $_SERVER['HTTP_HOST'];
                
                if($contactHandler->distribute_credentials($userHandler->temp_user_array))
                {
                    $userHandler->temp_user_array = array();
                    $jsonArray['success'] = TranslationHandler::get_static_text("ACCOUNT_PASS_ASSIGNED");
                    $jsonArray['status_value'] = true;
                }
                else
                {
                    $userHandler->temp_user_array = array();
                    $jsonArray['error'] = $contactHandler->error->title;
                    $jsonArray['status_value'] = false;
                }
            }
            else
            {
                $jsonArray['error'] = $userHandler->error->title;
                $jsonArray['status_value'] = false;
            }

            echo json_encode($jsonArray);
            die();
        break;
    }
    
    
}
?>

