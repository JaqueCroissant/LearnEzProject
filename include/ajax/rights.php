<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/pageHandler.php';

if(isset($_POST)) {
    $page_rights = (isset($_POST["page_rights"]) ? $_POST["page_rights"] : null);
    $user_type_id = (isset($_POST["user_type_id"]) ? $_POST["user_type_id"] : 0);
    
    if(RightsHandler::update_page_rights($user_type_id, $page_rights)) {
        $jsonArray['status_value'] = true;
    } else {
        $jsonArray['status_value'] = false;
        $jsonArray['error'] = RightsHandler::$error->title;
    }
    echo json_encode($jsonArray);
    die();
}

?>