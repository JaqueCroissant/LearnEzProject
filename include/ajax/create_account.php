<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';
    require_once '../../include/handler/classHandler.php';
    require_once '../../include/handler/schoolHandler.php';

    $userHandler = new UserHandler();
    $classHandler = new ClassHandler();
    $schoolHandler = new SchoolHandler();

    switch($_GET['step'])
    {

        case '1':
            if(isset($_POST))
            {
                $firstname = isset($_POST["firstname"]) ? $_POST["firstname"] : "";
                $surname = isset($_POST["surname"]) ? $_POST["surname"] : "";
                $email = isset($_POST["email"]) ? $_POST["email"] : "";
                $password = isset($_POST["password"]) ? $_POST["password"] : "";
                $usertype = isset($_POST["usertype"]) ? $_POST["usertype"] : "";

                $temp_school_id = $usertype == 'SA' ? "" : (isset($_POST["school_id"]) ? $_POST["school_id"] : "");
                $school_id = $userHandler->_user->user_type_id == 1 ? $temp_school_id : $userHandler->_user->school_id;

                $class_ids = $usertype == 'SA' || $usertype == 'A'  ? array() : (isset($_POST["class_name"]) ? $_POST["class_name"] : array());

                if($userHandler->create_new_profile($firstname, $surname, $email,
                $password, $usertype, $school_id, $class_ids))
                {
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

                $temp_id = isset($_POST["school_id"]) ? $_POST["school_id"] : "";
                $school_id = $userHandler->_user->user_type_id == 1 ? $temp_id : $userHandler->_user->school_id;
                $class_ids = isset($_POST["class_name"]) ? $_POST["class_name"] : array();

                $file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : array();

                if($userHandler->import_users($file, $school_id, $class_ids))
                {
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

        case 'get_classes':
            if(isset($_GET["school_id"]))
            {
                $jsonArray["classes"] = "";

                if($classHandler->get_classes_by_school_id($_GET["school_id"])) {
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