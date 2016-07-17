<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';

    $userHandler = new UserHandler();

    switch($_GET['step'])
    {
        //VERIFICER BRUGERINFO
        case "1":
            if(isset($_POST)) 
            {
                $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : "";
                $surname = isset($_POST['surname']) ? $_POST['surname'] : "";
                $email = isset($_POST['email']) ? $_POST['email'] : "";
                $description = isset($_POST['description']) ? $_POST['description'] : "";
                $avatar_id = isset($_POST['avatar_hidden_id']) ? $_POST['avatar_hidden_id'] : "";

                if($userHandler->edit_user_info($firstname, $surname, $email, $description, $avatar_id))
                {
                    $jsonArray['status_value'] = true;
                    $jsonArray['full_name'] = $userHandler->_user->firstname . " " . $userHandler->_user->surname;
                    $jsonArray['avatar_id'] = $userHandler->_user->image_id;
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
        
        //SKIFT PASSWORD
        case "2":
            if(isset($_POST))
            {
                $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : "";
                $new_password = isset($_POST['password']) ? $_POST['password'] : "";
                $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : "";

                if(!$userHandler->change_password($old_password, $new_password, $confirm_password))
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $userHandler->error->title;
                } 
                else 
                {
                    $jsonArray['status_value'] = true;
                }
                echo json_encode($jsonArray);
                die();
            }
        break;
    }
?>