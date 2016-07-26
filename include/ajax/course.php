<?php
require_once '../../include/ajax/require.php';
require_once '../../include/handler/courseHandler.php';

$courseHandler = new CourseHandler();

if(isset($_POST)) {
    $step = isset($_GET["step"]) ? $_GET["step"] : null;
    
    switch($step) {
        case "create_course":
            $os_id = isset($_POST["os"]) ? $_POST["os"] : 0;
            $points = isset($_POST["points"]) ? $_POST["points"] : 0;
            $title = isset($_POST["title"]) ? $_POST["title"] : array();
            $description = isset($_POST["description"]) ? $_POST["description"] : array();
            $language_ids = isset($_POST["language_id"]) ? $_POST["language_id"] : array();

            if($courseHandler->create_course($os_id, $points, $title, $description, $language_ids)) {
                $jsonArray['status_value'] = true;
                $jsonArray['success'] = TranslationHandler::get_static_text("COURSE_CREATED");
            } else {
                $jsonArray['status_value'] = false;
                $jsonArray['error'] = $courseHandler->error->title;
            }
            echo json_encode($jsonArray);
            die();
            break;
    }
}
    