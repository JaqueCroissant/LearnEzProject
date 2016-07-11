<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';

    $userHandler = new UserHandler();

    switch($_GET['step'])
    {
        case '1':
            if(isset($_POST))
            {
                if($userHandler->create_new_profile($_POST["firstname"], $_POST["surname"], $_POST["email"],
                $_POST["password"], $_POST["usertype"], $_POST["school_id"], $_POST["class_ids"]))
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
    }
?>