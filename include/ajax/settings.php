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
                if($userHandler->edit_user_info($_POST["firstname"], $_POST["surname"], $_POST["email"], $_POST["description"], $_POST["avatar_hidden_id"]))
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
                if(!$userHandler->change_password($_POST['old_password'], $_POST['password'], $_POST['confirm_password']))
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