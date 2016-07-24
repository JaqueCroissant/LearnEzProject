<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';
$rightsHandler = new RightsHandler();
if(isset($_POST)) {
    $user_type_id = (isset($_POST["user_type_id"]) ? $_POST["user_type_id"] : 0);
    
    if(isset($_POST["school_rights"])) {
        $rights = (isset($_POST["rights"]) ? $_POST["rights"] : array());

        if($rightsHandler->update_school_rights($user_type_id, $rights)) {
            $jsonArray['status_value'] = true;
            $jsonArray['success'] = TranslationHandler::get_static_text("SCHOOL_RIGHTS_UPDATED");
        } else {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = $rightsHandler->error->title;
        }
        echo json_encode($jsonArray);
        die();
    }
    
    if(isset($_POST["rights_type"])) {
        $rights = (isset($_POST["rights"]) ? $_POST["rights"] : array());

        if($rightsHandler->update_rights($user_type_id, $rights)) {
            $jsonArray['status_value'] = true;
            $jsonArray['success'] = TranslationHandler::get_static_text("GENERAL_RIGHTS_UPDATED");
        } else {
            $jsonArray['status_value'] = false;
            $jsonArray['error'] = $rightsHandler->error->title;
        }
        echo json_encode($jsonArray);
        die();
    }
    
    
    $page_rights = (isset($_POST["page_rights"]) ? $_POST["page_rights"] : array());
    
    if($rightsHandler->update_page_rights($user_type_id, $page_rights)) {
        $jsonArray['status_value'] = true;
        $jsonArray['success'] = TranslationHandler::get_static_text("PAGE_RIGHTS_UPDATED");
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = $rightsHandler->error->title;
    }
    echo json_encode($jsonArray);
    die();
}

?>