<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';
    require_once '../../include/handler/classHandler.php';

    $userHandler = new UserHandler();
    $classHandler = new ClassHandler();

    switch($_GET['step'])
    {

        case '1':
            if(isset($_POST))
            {
                $school_id = $userHandler->_user->user_type_id == 1 ? $_POST["school_id"] : $userHandler->_user->school_id;

                if($userHandler->create_new_profile($_POST["firstname"], $_POST["surname"], $_POST["email"],
                $_POST["password"], $_POST["usertype"], $school_id, $_POST["class_name"]))
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
                if($userHandler->import_users($_POST["csv_file"], $_POST["school_id"]))
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