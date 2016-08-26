<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';
    require_once '../../include/handler/classHandler.php';
    require_once '../../include/handler/schoolHandler.php';
    require_once '../../include/handler/contactHandler.php';

    $userHandler = new UserHandler();
    $classHandler = new ClassHandler();
    $schoolHandler = new SchoolHandler();
    $contactHandler = new ContactHandler();
    
    switch($_GET['step'])
    {
        case 'create_user':
            if(isset($_POST))
            {
                $firstname = isset($_POST["firstname"]) ? $_POST["firstname"] : "";
                $surname = isset($_POST["surname"]) ? $_POST["surname"] : "";
                $email = isset($_POST["email"]) ? $_POST["email"] : "";
                $password = isset($_POST["password"]) ? $_POST["password"] : "";
                $usertype = isset($_POST["usertype"]) ? $_POST["usertype"] : "";

                $temp_school_id = $usertype == 'SA' ? "" : (isset($_POST["school_id"]) ? $_POST["school_id"] : "");
                $school_id = $userHandler->_user->user_type_id == 1 ? $temp_school_id : $userHandler->_user->school_id;

                $class_ids = isset($_POST["class_name"]) ? $_POST["class_name"] : array();

                if(($usertype == 'S' || $usertype == 'T') && (!$schoolHandler->can_add_students($school_id) || !$schoolHandler->school_has_classes($school_id, $class_ids)))
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $schoolHandler->error->title;
                }
                else if($usertype == 'A' && !$schoolHandler->school_has_classes($school_id, $class_ids))
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $schoolHandler->error->title;
                }
                else
                {
                    if($userHandler->create_new_profile($firstname, $surname, $email, $password, $usertype, $school_id, $class_ids))
                    {
                        if($contactHandler->distribute_credentials($userHandler->users))
                        {
                            $jsonArray['status_value'] = true;
                            $jsonArray['success'] = TranslationHandler::get_static_text("CREATE_USER_SUCCESS");
                            $jsonArray['username'] = $userHandler->new_username;
                        }
                        else
                        {
                            $jsonArray['status_value'] = false;
                            $jsonArray['error'] = $contactHandler->error->title;
                        }
                    }
                    else
                    {
                        $jsonArray['status_value'] = false;
                        $jsonArray['error'] = $userHandler->error->title;
                    }
                }

                echo json_encode($jsonArray);
                die();
            }
        break;

        case 'import_users':
            if(isset($_POST))
            {
                $temp_school_id = isset($_POST["school_id"]) ? $_POST["school_id"] : "";
                $school_id = $userHandler->_user->user_type_id == 1 ? $temp_school_id : $userHandler->_user->school_id;
                $class_ids = isset($_POST["class_name"]) ? $_POST["class_name"] : array();

                $file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : array();

                if(!$schoolHandler->can_add_students($school_id) || !$schoolHandler->school_has_classes($school_id, $class_ids))
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $schoolHandler->error->title;
                }
                else
                {
                    if($userHandler->import_users($file, $school_id, $class_ids))
                    {
                        if($contactHandler->distribute_credentials($userHandler->users))
                        {
                            $jsonArray['status_value'] = true;
                            $jsonArray['success'] = TranslationHandler::get_static_text("IMPORT_USER_SUCCESS");
                        }
                        else
                        {
                            $jsonArray['status_value'] = false;
                            $jsonArray['error'] = $contactHandler->error->title;
                        }
                    }
                    else
                    {
                        $jsonArray['has_add_info'] = $userHandler->import_has_add_info;
                        $jsonArray['add_info'] = $userHandler->import_add_info;
                        $jsonArray['status_value'] = false;
                        $jsonArray['error'] = $userHandler->error->title;
                    }
                }
                echo json_encode($jsonArray);
                die();
            }
        break;

        case 'get_classes':
            if(isset($_GET["school_id"]))
            {
                $jsonArray["classes"] = "";

                if($classHandler->get_classes_by_school_id($_GET["school_id"], true)) {
                    foreach($classHandler->classes as $class)
                    {
                        $jsonArray["classes"] .= '<option value="' . $class->id . '">' . $class->title . '</option>'; 
                    }
                    $jsonArray['status_value'] = true;
                } else {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $classHandler->error->title;
                }
                echo json_encode($jsonArray);
                die();
            }
            break;

        default:
            echo $_GET['step'];
    }
?>