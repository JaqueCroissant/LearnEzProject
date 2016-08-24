<?php
    require_once '../../include/ajax/require.php';
    require_once '../../include/handler/userHandler.php';
    require_once '../../include/handler/mediaHandler.php';

    $userHandler = new UserHandler();
    $settingsHandler = new SettingsHandler();
    $mediaHandler = new MediaHandler();

    if(isset($_POST)) {
        $current_step = isset($_GET["step"]) ? $_GET["step"] : null;
        switch($current_step)
        {
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
                    $jsonArray['avatar_id'] = $userHandler->_user->profile_image;
                } 
                else 
                {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $userHandler->error->title;
                }
                echo json_encode($jsonArray);
            break;

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
                echo json_encode($jsonArray);
            break;

            case "preferences":
                $settings = SettingsHandler::get_settings();
                $settings->language_id = isset($_POST['language']) ? $_POST['language'] : 0;
                $settings->os_id = isset($_POST['os']) ? $_POST['os'] : 0;
                $settings->elements_shown = isset($_POST['elements_shown']) ? $_POST['elements_shown'] : 0;
                $settings->hide_profile = isset($_POST['hide_profile']) ? true : false;
                $settings->block_mail_notifications = isset($_POST['block_mail_notifications']) ? true : false;
                $settings->block_student_mails = isset($_POST['block_student_mails']) ? true : false;
                $settings->blocked_students = isset($_POST['blocked_student']) ? $_POST['blocked_student'] : array();

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
                echo json_encode($jsonArray);
            break;
            
            case "upload_profile_image":
                $file = isset($_FILES["profile_image_upload"]) ? $_FILES["profile_image_upload"] : null;
                if($mediaHandler->upload_profile_image($file)) {
                    $jsonArray['status_value'] = true;
                    $jsonArray['success'] = TranslationHandler::get_static_text("PROFILE_IMAGE_UPLOADED");
                } else {
                    $jsonArray['status_value'] = false;
                    $jsonArray['error'] = $mediaHandler->error->title;
                }
                echo json_encode($jsonArray);
            break;
        }
        
    }
    
    if(isset($_GET["delete_profile_image"]) && isset($_GET["profile_image_id"])) {
        if($mediaHandler->delete_profile_image($_GET["profile_image_id"])) {
            $jsonArray['success'] = TranslationHandler::get_static_text("PROFILE_IMAGE_DELETED");
            $jsonArray['status_value'] = true;
        } else {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = $mediaHandler->error->title;
        }
        echo json_encode($jsonArray);
    }
    
    if(isset($_GET["get_profile_images"])) {
        if(!$mediaHandler->get_profile_images()) {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = $mediaHandler->error->title;
            echo json_encode($jsonArray);
            die();
        }
        
        $selected_profile_image = isset($_GET["selected_profile_image"]) ? $_GET["selected_profile_image"] : 0;
        if(!empty($mediaHandler->profile_images)) {
            $jsonArray["profile_images"] = "";
            foreach($mediaHandler->profile_images as $value) {
                $jsonArray["profile_images"] .= '<div class="avatar avatar-xl profile_image_element" profile_image_id="' . $value['id'] . '" style="cursor:pointer;z-index:10;'. ($selected_profile_image > 0 && $selected_profile_image == $value['id'] ? '' : ($selected_profile_image > 0 ? 'opacity: 0.5' : '')) .'"><div class="delete_profile_image delete_profile_image_style hidden" title="'.TranslationHandler::get_static_text("DELETE_PROFILE_IMAGE").'" profile_image_id="' . $value['id'] . '"><i class="zmdi zmdi-close" style="display:initial !important;"></i></div><img style="border-radius: 100% !important;" src="assets/images/profile_images/' . $value['filename'] . '"/><div class="active_profile_image '. ($selected_profile_image > 0 && $selected_profile_image == $value['id'] ? '' : 'hidden') .'" title="'.TranslationHandler::get_static_text("PICK_PROFILE_IMAGE").'" profile_image_id="' . $value['id'] . '"><i class="zmdi zmdi-check" style="display:initial !important;"></i></div></div>';
            }
            $jsonArray['status_value'] = true;
        } else {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = $mediaHandler->error->title;
        }
        echo json_encode($jsonArray);
    }
?>