<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';

    $userHandler = new UserHandler();
    $settingsHandler = new SettingsHandler();

    if(isset($_POST)) {
        $current_step = isset($_GET["step"]) ? $_GET["step"] : null;
        switch($current_step)
        {
            //VERIFICER BRUGERINFO
            case "edit_info":
                $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : "";
                $surname = isset($_POST['surname']) ? $_POST['surname'] : "";
                $email = isset($_POST['email']) ? $_POST['email'] : "";
                $description = isset($_POST['description']) ? $_POST['description'] : "";
                $avatar_id = isset($_POST['avatar_hidden_id']) ? $_POST['avatar_hidden_id'] : "";

                if($userHandler->edit_user_info($firstname, $surname, $email, $description, $avatar_id))
                {
                    $jsonArray['status_value'] = true;
                    $jsonArray['success'] = TranslationHandler::get_static_text("EDIT_INFO_SUCCESS");
                    $jsonArray['full_name'] = $userHandler->_user->firstname . " " . $userHandler->_user->surname;
                    $jsonArray['avatar_id'] = $userHandler->_user->image_id;
                } 
                else 
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $userHandler->error->title;
                }
            break;

            //SKIFT PASSWORD
            case "change_password":
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
                    $jsonArray['success'] = TranslationHandler::get_static_text("CHANGE_PASSWORD_SUCCESS");
                }
            break;

            case "preferences":
                $settings = SettingsHandler::get_settings();
                $settings->language_id = isset($_POST['language']) ? $_POST['language'] : 0;
                $settings->os_id = isset($_POST['os']) ? $_POST['os'] : 0;
                $settings->elements_shown = isset($_POST['elements_shown']) ? $_POST['elements_shown'] : 0;
                $settings->hide_profile = isset($_POST['hide_profile']) ? true : false;
                $settings->block_mail_notifications = isset($_POST['block_mail_notifications']) ? true : false;
                $settings->block_student_mails = isset($_POST['block_student_mails']) ? true : false;

                if($settingsHandler->update_settings($settings))
                {
                    $jsonArray['status_value'] = true;
                    $jsonArray['reload'] = true;
                    $jsonArray['success'] = TranslationHandler::get_static_text("EDIT_PREFERENCES");
                } 
                else 
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $settingsHandler->error->title;
                }
            break;
        }
        echo json_encode($jsonArray);
    }
?>