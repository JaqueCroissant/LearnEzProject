<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/mediaHandler.php';

$mediaHandler = new MediaHandler();

if(isset($_POST)) {
    $step = isset($_GET["step"]) ? $_GET["step"] : null;
    
    switch($step) {
        case "delete_test":
            $file_name = isset($_GET["file_name"]) ? $_GET["file_name"] : null;
            if($mediaHandler->delete("tests/" . $file_name)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("TEST_DELETED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $mediaHandler->error->title;
            }
            echo json_encode($jsonArray);
            break;
    }
}